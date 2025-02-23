<?php

namespace App\Filament\Reports;

use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\Body\TextColumn;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use App\Models\Order;

class OrderReport extends Report
{
    public ?string $heading = "Report Order";

    // public ?string $subHeading = "A great report";

    public function header(Header $header): Header
    {
        return $header
            ->schema([
                // ...
                Header\Layout\HeaderRow::make()
            ->schema([
                Header\Layout\HeaderColumn::make()
                    ->schema([
                        Text::make("Order sales report")
                            ->title()
                            ->primary(),
                        Text::make("Order sales report")
                            ->subtitle(),
                    ]),
                Header\Layout\HeaderColumn::make()
                    ->schema([
                        
                    ])
                    ->alignRight(),
            ]),
            ]);
    }


    public function body(Body $body): Body
    {
        return $body
            ->schema([
                // ...
                Body\Layout\BodyColumn::make()
                ->schema([
                     Body\Table::make()
                     ->columns([
                                TextColumn::make("order_id")->label('Order ID'),
                                TextColumn::make('total_amount')->label('Total Amount')->money('idr'),
                                TextColumn::make('status')->label('Status')->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                                TextColumn::make('order_date')->label('Date Order'),
                            ])
                        ->data(
                            fn(?array $filters) => Order::query()->where('status', $filters)->get()
                        ),
                ]),
            ]);
    }

    public function footer(Footer $footer): Footer
    {
        return $footer
            ->schema([
                // ...
                 Footer\Layout\FooterRow::make()
                ->schema([
                    Footer\Layout\FooterColumn::make()
                        ->schema([
                            Text::make("Pos Lite")
                                ->title()
                                ->primary(),
                        ]),
                    Footer\Layout\FooterColumn::make()
                        ->schema([
                            Text::make("Generated on: " . now()->format('Y-m-d H:i:s')),
                        ])
                        ->alignRight(),
                ]),
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                // ...
                Select::make('status')
                ->options([
                    'paid' => 'Paid',
                    'pending' => 'Pending',
                ]),
            ]);
    }
}
