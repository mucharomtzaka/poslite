<?php

namespace App\Filament\Resources\ProductLocationsResource\Pages;

use App\Filament\Resources\ProductLocationsResource;
use Filament\Actions;
use App\Models\ProductLocations;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateProductLocations extends CreateRecord
{
    protected static string $resource = ProductLocationsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Extract location ID
        $locationId = $data['location_id'];

        // Extract product stock entries
        $productLocations = $data['product_locations'];

        // Bulk Insert
        $records = [];
        foreach ($productLocations as $entry) {
            $records[] = [
                'location_id' => $locationId,
                'product_id' => $entry['product_id'],
                'stock_quantity' => $entry['stock_quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert into database
        ProductLocations::insert($records);

        // Show success notification
        Notification::make()
            ->title('Products assigned to location successfully!')
            ->success()
            ->send();

        return new ProductLocations(); // Return an empty model (since it's bulk insert)
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
