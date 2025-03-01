<?php

namespace App\Filament\Resources\ProductLocationsResource\Pages;

use App\Filament\Resources\ProductLocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductLocations extends EditRecord
{
    protected static string $resource = ProductLocationsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
