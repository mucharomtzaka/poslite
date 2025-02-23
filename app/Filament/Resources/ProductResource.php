<?php

namespace App\Filament\Resources;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use DesignTheBox\BarcodeField\Forms\Components\BarcodeInput;
use App\Filament\Resources\SupplierResource;
use App\Filament\Resources\CategoriesResource;
use App\Filament\Exports\ProductExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required()
                    ->createOptionForm(CategoriesResource::getFormSchema())
                    ->createOptionModalHeading('Create Category')
                    ->searchable(),
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->createOptionForm(SupplierResource::getFormSchema())
                    ->createOptionModalHeading('Create Supplier')
                    ->searchable(),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('price')->numeric()->required(),
                Forms\Components\TextInput::make('cost_price')->numeric()->required(),
                Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                BarcodeInput::make('barcode')->label('Barcode')->nullable(),
                Forms\Components\TextInput::make('stock_quantity')->label('Stock Qty')->numeric()->required()->disabledOn('edit'),
                Forms\Components\TextInput::make('min_stock_level')->label('Stock Min')->numeric()->required()->default(0),
                Forms\Components\Textarea::make('description')->rows(5)->cols(20)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) 
            ->headerActions([
                ExportAction::make('Export')
                    ->exporter(ProductExporter::class)->formats([
                        ExportFormat::Xlsx,
                    ])->fileDisk('public')
            ])
            ->columns([
                //
                TextColumn::make('sku'),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('price')->money('idr'),
                TextColumn::make('cost_price')->money('idr'),
                TextColumn::make('stock_quantity'),
                TextColumn::make('min_stock_level'),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('supplier.name')->label('Supplier'),
                Tables\Columns\TextColumn::make('description')->limit(100),
            ])
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),   
                ]),
                ExportBulkAction::make('Export')->exporter(ProductExporter::class)->formats([
                    ExportFormat::Xlsx,
                ])->fileDisk('public')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
