<?php

namespace App\Filament\Resources\ProductLocationsResource\Pages;

use App\Filament\Resources\ProductLocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductLocations extends ListRecords
{
    protected static string $resource = ProductLocationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
