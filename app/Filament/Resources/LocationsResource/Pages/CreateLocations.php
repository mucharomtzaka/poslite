<?php

namespace App\Filament\Resources\LocationsResource\Pages;

use App\Filament\Resources\LocationsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocations extends CreateRecord
{
    protected static string $resource = LocationsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array{ 
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array{
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
