<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;

class SalesOverview extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview';

    protected function getData(): array
    {

         $salesData = Order::selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $salesData->pluck('total')->toArray(),
                    'backgroundColor' => '#3b82f6', // Blue color
                ],
            ],
            'labels' => $salesData->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
