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
use Filament\Support\Enums\ActionSize;

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
                    ->required()->default(1)
                    ->createOptionForm(CategoriesResource::getFormSchema())
                    ->createOptionModalHeading('Create Category')
                    ->searchable(),
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->default(1)
                    ->createOptionForm(SupplierResource::getFormSchema())
                    ->createOptionModalHeading('Create Supplier')
                    ->searchable(),
                Forms\Components\TextInput::make('name')->label('Name Product')->required(),
                Forms\Components\TextInput::make('price_sale')->label('Price Sale')->numeric()->required()->default(0),
                Forms\Components\TextInput::make('price_purchase')->label('Price Purchase')->numeric()->required()->default(0),
                Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                BarcodeInput::make('barcode')->label('Barcode')->nullable(),
                Forms\Components\TextInput::make('stock_quantity')->label('Stock Qty')->numeric()->disabled()->default(0),
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
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('name')->label('Name Product')->sortable()->searchable(),
                TextColumn::make('price_sale')->label('Price Sale')->money('idr'),
                TextColumn::make('price_purchase')->label('Price Purchase')->money('idr'),
                TextColumn::make('stock_quantity')->default(0),
                TextColumn::make('min_stock_level')->default(0),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('supplier.name')->label('Supplier')->default('-'),
                Tables\Columns\TextColumn::make('description')->limit(100),
            ])
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                ])->label('More actions')
                ->icon('heroicon-m-ellipsis-vertical')
                 ->size(ActionSize::Small)
                 ->color('primary')
                 ->button(),
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
