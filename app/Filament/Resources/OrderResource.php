<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product; 
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Enums\ActionSize;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $label = 'Order Sales';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'first_name')
                    ->required()->searchable(['first_name', 'email'])
                    ->createOptionForm([
                        Forms\Components\TextInput::make('first_name')->required(),
                        Forms\Components\TextInput::make('last_name')->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\TextInput::make('phone')->required(),
                        Forms\Components\Textarea::make('address')->rows(5)->cols(20),
                    ]),
                    Section::make()->schema([
                        Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Product')
                                ->relationship('product', 'name')
                                ->required()
                                ->reactive() // Allows real-time updates
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Auto-update unit_price when product is selected
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', number_format($product->price,2));
                                    }
                                }),
                            Forms\Components\TextInput::make('unit_price')->readonly(), // Hidden field for unit_price
                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    // Auto-update total_price when quantity changes
                                    $productId = $get('product_id');
                                    $product = Product::find($productId);
                                    
                                    if ($product) {
                                        $set('unit_price', number_format($product->price,2));
                                        $set('total_price', $product->price * $state);
                                    }
                                }),
                                Forms\Components\Hidden::make('total_price'), // Hidden field for total_price
                        ])
                        ->orderable('order_item_id')
                        ->defaultItems(1)
                        ->minItems(1)
                        ->createItemButtonLabel('Add Product')
                        ->columns(3)
                    ]),
                    Section::make() ->schema([
                        Placeholder::make('total_amount')
                    ->label('Total Amount')
                    ->content(function (callable $get,callable $set) {
                        // Calculate total from all items
                        $total = 0;
                        $items = $get('items');
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $total += $item['total_price'] ?? 0;
                            }
                        }
                        $set('total_amount', $total);
                        return 'IDR ' . number_format($total, 2);
                    }),
                        ])->columns(4),
                        Forms\Components\Hidden::make('total_amount'),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) 
            ->columns([
                //
                TextColumn::make('order_id')->label('Order ID')->sortable()->searchable(),
                TextColumn::make('order_date')->label('Date Order'),
                TextColumn::make('customer.first_name')->label('Customer')->searchable(),
                TextColumn::make('total_amount')->label('Total Amount')->money('idr'),
                TextColumn::make('status')->label('Status')->searchable()->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([

              Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('payment')
                ->icon('heroicon-o-credit-card')
                ->disabled(fn (Order $record): bool => $record->status === 'paid')
                ->form(fn (Order $record) => [
                    Forms\Components\TextInput::make('order_id')
                        ->label('Order ID')
                        ->default($record->order_id) // Set the default order_id from the selected order
                        ->readonly(), // Prevent modification,
                    Forms\Components\DatePicker::make('payment_date')->default(now())->required(),
                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'credit_card' => 'Credit Card',
                            'debit_card' => 'Debit Card',
                        ])
                        ->required(),
                        Forms\Components\Placeholder::make('total_payment')
                        ->label('Total Payment ')
                        ->content(function (callable $get) use ($record) {
                            return 'IDR ' . number_format($record->total_amount, 2);
                        }),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->live(debounce: 500) // Prevents excessive updates
                            ->afterStateUpdated(function ($state, callable $get, callable $set) use ($record) {
                                // Ensure we have a valid total amount
                                $totalAmount = $record ? (float) $record->total_amount : 0;
                                $totalPaid = is_numeric($state) ? (float) $state : 0;

                                // Calculate balance safely
                                $balance = $totalPaid - $totalAmount;
                                
                                // Ensure Livewire updates correctly
                                $set('balance', max($balance, 0)); // Prevent negative values if needed
                            }),
                    Forms\Components\Placeholder::make('Balance')
                        ->label('Balance')
                        ->content(function (callable $get) {
                            $balance = (float) $get('balance') ?? 0;
                            return 'IDR ' . number_format($balance, 2);
                        }),
                    ])
                ->action(function (array $data)  {
                
                     DB::transaction(function () use ($data) {
                         Payment::create([
                                'order_id' => $data['order_id'],
                                'payment_date' => $data['payment_date'],
                                'payment_method' => $data['payment_method'],
                                'amount' => $data['amount'],
                        ]);

                         $order = Order::find($data['order_id']);

                        if ($order->total_amount == $data['amount'] || $data['amount'] > 0) {
                                $status = 'paid';
                            }  else {
                                $status = 'pending';
                            }

                        $order->update(['status' => $status]);

                        // ✅ Reduce stock for each product in the order
                        foreach ($order->orderItems  as $item) {
                                $product = Product::find($item->product_id);
                                if ($product) {
                                    $newStock = max($product->stock_quantity - $item->quantity, 0); // Prevent negative stock
                                    $product->update(['stock_quantity' => $newStock]);
                                }
                         }
                    });
                  Notification::make()
                    ->title('Payment successfully')
                    ->success()
                    ->send();

             })
            ->modalHeading('Make Payment')
            ->modalButton('Save Payment')
            ->modalWidth('md'),
                Tables\Actions\Action::make('Change Status')->icon('heroicon-o-pencil-square')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'cancel' => 'Cancel',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, Order $record) {
                        $record->update(['status' => $data['status']]);
                    })
                    ->modalHeading('Update Order Status')
                    ->modalButton('Save Changes')
                    ->modalWidth('md'),
                Tables\Actions\Action::make('print')
                    ->label('Print Order')
                    ->icon('heroicon-o-printer')
                    ->action(fn (Order $record) => self::generateOrderPDF($record))
                    ->color('primary')
                    ->url(fn (Order $record) => route('orders.print', $record->order_id))
                    ->openUrlInNewTab()
                    ->disabled(fn (Order $record) => $record->status === 'cancel'),
                Tables\Actions\EditAction::make()->disabled(fn (Order $record): bool => $record->status === 'paid'),
                Tables\Actions\DeleteAction::make()->disabled(fn (Order $record): bool => $record->status === 'paid'),
                Tables\Actions\RestoreAction::make(),
             ])
            ->label('More actions')
            ->icon('heroicon-m-ellipsis-vertical')
            ->size(ActionSize::Small)
            ->color('primary')
            ->button(),
               
             ])
            ->bulkActions([
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
