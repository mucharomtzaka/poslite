<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryLogResource\Pages;
use App\Filament\Resources\InventoryLogResource\RelationManagers;
use App\Models\InventoryLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class InventoryLogResource extends Resource
{
    protected static ?string $model = InventoryLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reports';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('log_id')->label('Log ID')->sortable()->searchable(),
                TextColumn::make('product.name')->label('Name Product')->sortable()->searchable(),
                TextColumn::make('change_type')->label('Change Type')->sortable()->searchable(),
                TextColumn::make('quantity_change')->label('Quantity')->sortable()->searchable(),
                TextColumn::make('reason')->label('Reason'),
                TextColumn::make('operator.name')->label('Logged By'),
                TextColumn::make('log_date')->label('Log Date'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListInventoryLogs::route('/'),
        ];
    }
}
