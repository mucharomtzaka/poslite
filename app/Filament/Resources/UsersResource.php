<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\SupabaseStorageService;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')
                                            ->required() // cannot empty
                                            ->maxLength(255), // max char 255

                Forms\Components\TextInput::make('email')
                                            ->required() // cannot empty
                                            ->email() // email validation
                                            ->maxLength(255), // max char 255

                Forms\Components\TextInput::make('password')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->revealable() // hide show password
                                ->maxLength(255), // max char 255
                FileUpload::make('file')->label('Avatar')
                ->storeFileNamesIn('original_name')
                ->maxSize(51200)
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('avatar', app(SupabaseStorageService::class)->upload($state))
                ),
                Forms\Components\Hidden::make('avatar')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null) 
            ->columns([
                //
                ImageColumn::make('avatar')->label('Picture')->circular()
                ->defaultImageUrl(url('/images/product-placeholder.jpg'))
                ->getStateUsing(fn ($record) => app(SupabaseStorageService::class)->getFileUrl($record->avatar)),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}
