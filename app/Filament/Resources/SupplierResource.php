<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Master Data';

    public static function getFormSchema(): array
    {
        return [
            Select::make('default_location_id')
            ->label('Default Location')
            ->relationship('defaultLocation', 'name')
            ->searchable()
            ->nullable(),
            Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('contact_name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('phone')->required(),
            Forms\Components\Textarea::make('address')->rows(5)->cols(20)->required(),
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
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('contact_name')->sortable()->searchable(),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('address')->limit(100),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
