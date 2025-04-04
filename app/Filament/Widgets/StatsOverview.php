<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Cards\PieChart\PieChartCard;
use App\Filament\Widgets\Cards\PieChart\Slice;
use App\Filament\Widgets\Cards\Stack;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ReparkRequest;
use App\Models\Tenant\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected function getCards(): array
    {
        $access_status_counts = Access::getStatusesCount()->first();
        $repark_request_status_counts = ReparkRequest::getStatusesCount()->first();
        $drivers = Driver::count();
        $vehicles = Vehicle::count();

        return [
            PieChartCard::make('Accesses', $access_status_counts->total_count)
                ->slices([
                    Slice::make('Issued', $access_status_counts->issued_count),
                    Slice::make('Expired', $access_status_counts->expired_count),
                    Slice::make('Inactive', $access_status_counts->inactive_count),
                    Slice::make('Active', $access_status_counts->active_count),
                ])->size('sm'),

            PieChartCard::make('Repark Requests', $repark_request_status_counts->total_count)
                ->slices([
                    Slice::make('Unresolved', $repark_request_status_counts->unresolved_count),
                    Slice::make('Pending', $repark_request_status_counts->pending_count)->color('yellow'),
                    Slice::make('Resolved', $repark_request_status_counts->resolved_count)->color('green'),
                ])->size('sm'),

            Stack::make([
                Card::make('No of drivers registered', $drivers),
                Card::make('No of Vehicles registered', $vehicles),

                Card::make('Help', '')
                    ->description(str('<span class="text-xs font-normal">Stats show you important metrics about your organisation.</span>')->toHtmlString())
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                    ])
                    ->url('help')
                    ->openUrlInNewTab(),
            ])->space(4),
        ];
    }
}
