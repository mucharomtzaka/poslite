<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseTransactionResource\Pages;
use App\Filament\Resources\PurchaseTransactionResource\RelationManagers;
use App\Models\PurchaseTransaction;
use App\Models\Product; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;

class PurchaseTransactionResource extends Resource
{
    protected static ?string $model = PurchaseTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $label = 'Order Purchase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable(),
                DatePicker::make('purchase_date')
                    ->default(now())
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->disabled(),
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                            TextInput::make('purchase_price')->label('Purchase Price Item')
                            ->numeric()
                            ->required()
                            ->live(debounce: 1000)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Auto-update total_price when quantity changes
                                $set('total_price', $state * $get('quantity'));
                            }),
                            Hidden::make('total_price'),
                    ])
                    ->columns(3)
                    ->defaultItems(1) // ✅ Ensure it shows at least one item initially
                    ->minItems(1)     // ✅ Require at least one item
                    ->addable(true)   // ✅ Allow adding items
                    ->deletable(true) // ✅ Allow removing items
                    ->reorderable(true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $items = $get('items') ?? [];
                        // Update state with new values
                         $normalizedItems = array_values($items);
                         $set('item_detail', $normalizedItems);
                    }),
                    Hidden::make('item_detail'),
                    Placeholder::make('total_purchase')
                    ->label('Total Purchase')
                    ->content(function (callable $get, callable $set, ?PurchaseTransaction $record) {
                        // If editing, get total_purchase from the existing record
                        if ($record) {
                            $total_purchase = $record->purchase_price ?? 0;
                        } else {
                            // If creating, calculate from form inputs
                            $total_purchase = 0;
                            $items = $get('items') ?? [];
                            if (is_array($items)) {
                                foreach ($items as $item) {
                                    $total_purchase += $item['total_price'] ?? 0;
                                }
                            }
                        }
                        // Set the hidden field value
                        $set('total_purchase', $total_purchase);

                        return 'IDR ' . number_format($total_purchase, 2);
                    })
                    ->live() ,
                    Placeholder::make('total_quantity')
                    ->label('Total Quantity')
                    ->content(function (callable $get,callable $set) {
                        // Calculate total from all items
                        $total_qty = 0;
                        $total_purchase = 0;
                        $items = $get('items');
                        if (is_array($items)) {
                            foreach ($items as $item) {
                                $total_qty += $item['quantity'] ?? 0;
                            }
                        }
                        $set('total_quantity', $total_qty);
                        return $total_qty;
                    }),
                    Hidden::make('total_quantity')->default(0),   
                    Hidden::make('total_purchase')->default(0)
            ]);
    } 

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('supplier.name')->sortable()->searchable(),
                TextColumn::make('purchase_date')->date()->sortable(),
                TextColumn::make('purchase_price')->money('idr'),
                TextColumn::make('user.name')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function (PurchaseTransaction $record) {
                    // Start a transaction
                    \DB::transaction(function () use ($record) {
                        // Fetch items
                        $items = $record->items()->get();

                        // Reverse stock changes
                        foreach ($items as $item) {
                            \App\Models\Product::where('product_id', $item->product_id)
                                ->decrement('stock_quantity', $item->quantity);
                        }

                        // Delete related records
                        $record->items()->delete();
                        \App\Models\InventoryLog::where('purchase_transaction_id', $record->id)->delete();
                    });
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPurchaseTransactions::route('/'),
            'create' => Pages\CreatePurchaseTransaction::route('/create'),
            'view' => Pages\ViewPurchaseTransaction::route('/{record}'),
            'edit' => Pages\EditPurchaseTransaction::route('/{record}/edit'),
        ];
    }
}
