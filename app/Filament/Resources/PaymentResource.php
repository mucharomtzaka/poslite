<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 3;

    public static function getFormSchema():array{
        return [
                Section::make()
                 ->schema([
                        Forms\Components\Select::make('order_id')
                        ->label('Order')
                        ->relationship('listorder', 'order_id')
                        ->required()->searchable()
                        ->reactive() // Allows real-time updates
                        ->afterStateUpdated(function ($state, callable $set) {

                            // Auto-update amount when order id is selected
                            $order = Order::find($state);
    
                            if ($order) {
                                $set('total_amount', $order->total_amount);
                                $set('amount', $order->total_amount);
                            }
                        }),
                        Forms\Components\DatePicker::make('payment_date')->default(now())
                        ->required(),
                        Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'credit_card' => 'Credit Card',
                            'debit_card' => 'Debit Card',
                        ])
                        ->required(),
                ])->columns(3),
                Section::make()->schema([
                    Forms\Components\Placeholder::make('remaining_balance')
                    ->label('Total Paid')
                    ->content(function (callable $get , callable $set) {
                        $totalAmount = (float) $get('total_amount') ?? 0;
                        return 'IDR ' . number_format( $totalAmount, 2);
                    }),
                    Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->live(debounce: 500) // Prevents excessive updates
                    ->afterStateUpdated(function ($state, callable $get,callable $set) {
                           $totalAmount = (float) $get('total_amount') ?? 0;
                           $totalPaid = (float) $get('amount') ?? 0;
                           $set('balance', $totalPaid - $totalAmount);
                    }),
                    Forms\Components\Placeholder::make('Balance')
                    ->label('Balance')
                    ->content(function (callable $get , callable $set) {
                        $balance = (float) $get('balance') ?? 0;
                        return 'IDR ' . number_format( $balance, 2);
                    }),
                ])->columns(3),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) 
            ->columns([
                //
                Tables\Columns\TextColumn::make('payment_id')->label('Payment ID'),
                Tables\Columns\TextColumn::make('order.order_id')->label('Order ID'),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('amount')->money('idr'),
                Tables\Columns\TextColumn::make('payment_date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
