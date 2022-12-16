<?php

namespace App\Filament\Widgets;

use App\Models\Tenant\ReparkRequest;
use Filament\Widgets\BarChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ReparkRequestsChart extends BarChartWidget
{
    protected static ?string $heading = 'Repark Requests';

    protected static ?string $pollingInterval = '15s';

    protected static ?int $sort = 3;

    public ?string $filter = 'year';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        return match ($activeFilter) {
            'day' => $this->getDailyData(),
            'week' => $this->getWeeklyData(),
            'month' => $this->getMonthlyData(),
            'year' => $this->getYearlyData(),
            default => []
        };
    }

    protected function getFilters(): ?array
    {
        return [
            'day' => 'Today',
            'week' => 'Last Week',
            'month' => 'Last Month',
            'year' => 'This year',
        ];
    }

    protected function getYearlyData()
    {
        $data = Trend::model(ReparkRequest::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                \array_merge(
                    [
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    ],
                    $this->getBarChartDisplayConfig()
                ),
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
        ];
    }

    protected function getMonthlyData()
    {
        $data = Trend::model(ReparkRequest::class)
            ->between(
                start: now()->subMonth(),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                \array_merge(
                    [
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    ],
                    $this->getBarChartDisplayConfig()
                ),
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M jS')),
        ];
    }

    protected function getWeeklyData()
    {
        $data = Trend::model(ReparkRequest::class)
            ->between(
                start: now()->subWeek(),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                \array_merge(
                    [
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    ],
                    $this->getBarChartDisplayConfig()
                ),
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M jS')),
        ];
    }

    protected function getDailyData()
    {
        $data = Trend::model(ReparkRequest::class)
            ->between(
                start: now()->startOfDay(),
                end: now()->endOfDay(),
            )
            ->convertTimezone('+00:00', '+01:00')
            ->perHour()
            ->count();

        return [
            'datasets' => [
                \array_merge(
                    [
                        'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    ],
                    $this->getBarChartDisplayConfig()
                ),
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('h:i A')),
        ];
    }

    protected function getBarChartDisplayConfig()
    {
        return [
            'label' => 'Repark Requests',
            'backgroundColor' => 'rgb(119 122 261)',
            'borderColor' => 'rgb(119 122 261)',
        ];
    }
}
