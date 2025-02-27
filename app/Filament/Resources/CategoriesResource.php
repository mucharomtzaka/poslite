<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;
use App\Models\Categories;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Strings\Str;
use Filament\Forms\Components\Grid;
use App\Services\SupabaseStorageService;

class CategoriesResource extends Resource
{
    protected static ?string $model = Categories::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Master Data';


    public static function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema([
                FileUpload::make('file')->label('Picture')
                ->storeFileNamesIn('original_name')
                ->maxSize(51200)
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('picture', app(SupabaseStorageService::class)->upload($state))
                ),
                Forms\Components\TextInput::make('name')->unique(ignoreRecord: true)
                ->required() // cannot empty
                ->maxLength(255), 
                Forms\Components\Textarea::make('description')->rows(5)->cols(20),
                Forms\Components\Hidden::make('picture')
            ])     
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
                ImageColumn::make('picture')->label('Picture')->circular()
                ->defaultImageUrl(url('/images/product-placeholder.jpg'))
                ->getStateUsing(fn ($record) => 
                    !empty($record->picture) 
                        ? app(SupabaseStorageService::class)->getFileUrl($record->picture) 
                        : url('/images/product-placeholder.jpg')
                ),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(100),
            ])
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
            'edit' => Pages\EditCategories::route('/{record}/edit'),
        ];
    }
}
