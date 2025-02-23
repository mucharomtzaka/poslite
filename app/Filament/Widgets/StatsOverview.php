<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Customer;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
             Stat::make('Order Pending', 'IDR ' . number_format(Order::where('status','pending')->sum('total_amount'), 2)),
             Stat::make('Order Paid', 'IDR ' . number_format(Order::where('status','paid')->sum('total_amount'), 2)),
             Stat::make('Order Cancel', 'IDR ' . number_format(Order::where('status','cancel')->sum('total_amount'), 2)),
             Stat::make('Total Customer',Customer::count())
        ];
    }
}
