<?php

namespace App\Filament\Widgets\Cards\PieChart;

use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class PieChartCard extends Card
{
    public bool $showTotalLabel = true;

    public string $size = 'md';

    protected array $slices = [];

    public array $colors = [
        'slate' => 'bg-slate-500',
        'gray' => 'bg-gray-500',
        'zinc' => 'bg-zinc-500',
        'neutral' => 'bg-neutral-500',
        'stone' => 'bg-stone-500',
        'red' => 'bg-red-500',
        'orange' => 'bg-orange-500',
        'amber' => 'bg-amber-500',
        'yellow' => 'bg-yellow-500',
        'lime' => 'bg-lime-500',
        'green' => 'bg-green-500',
        'emerald' => 'bg-emerald-500',
        'teal' => 'bg-teal-500',
        'cyan' => 'bg-cyan-500',
        'sky' => 'bg-sky-500',
        'blue' => 'bg-blue-500',
        'indigo' => 'bg-indigo-500',
        'violet' => 'bg-violet-500',
        'purple' => 'bg-purple-500',
        'fuchsia' => 'bg-fuchsia-500',
        'pink' => 'bg-pink-500',
        'rose' => 'bg-rose-500',
    ];

    public array $hexColors = [
        'slate' => '#64748b',
        'gray' => '#6b7280',
        'zinc' => '#71717a',
        'neutral' => '#737373',
        'stone' => '#78716c',
        'red' => '#ef4444',
        'orange' => '#f97316',
        'amber' => '#f59e0b',
        'yellow' => '#eab308',
        'lime' => '#84cc16',
        'green' => '#22c55e',
        'emerald' => '#10b981',
        'teal' => '#14b8a6',
        'cyan' => '#06b6d4',
        'sky' => '#0ea5e9',
        'blue' => '#3b82f6',
        'indigo' => '#6366f1',
        'violet' => '#8b5cf6',
        'purple' => '#a855f7',
        'fuchsia' => '#d946ef',
        'pink' => '#ec4899',
        'rose' => '#f43f5e',
    ];

    public array $choices = [
        'red',
        'orange',
        'yellow',
        'green',
        'blue',
        'indigo',
        'purple',
        'pink',
        'gray',
    ];

    protected ?array $cachedData = null;

    public static function make(string $label, $value = null): static
    {
        return app(static::class, ['label' => $label, 'value' => $value]);
    }

    public function disableTotalLabel(bool $show = true): static
    {
        $this->showTotalLabel = ! $show;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function slices(array $slices): static
    {
        $this->slices = $slices;

        return $this;
    }

    public function shouldShowTotalLabel(): bool
    {
        return $this->showTotalLabel;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function getSlices(): array
    {
        return $this->slices;
    }

    public function getTotalValue(): int
    {
        return $this->getValue() ??
            Collection::make($this->getCachedData())
            ->sum(function (Slice $item) {
                return $item->getValue();
            });
    }

    public function getCachedData(): array
    {
        return $this->cachedData ??= $this->getData();
    }

    public function getStylesBackground(): string
    {
        $currentDegree = 0;
        $totalSlices = count($this->getCachedData());

        return Collection::make($this->getCachedData())
            ->map(function (Slice $slice, int $index) use (&$currentDegree, $totalSlices) {
                $percentage = $slice->getPercentage();
                $sliceDegree = ceil($percentage / 100 * 360);

                if ($index === 0) {
                    $startDegree = 0;
                    $endDegree = $sliceDegree;
                } elseif ($index === $totalSlices) {
                    $startDegree = $currentDegree;
                    $endDegree = 360;
                } else {
                    $startDegree = $currentDegree;
                    $endDegree = $currentDegree + $sliceDegree;
                }

                $currentDegree += $sliceDegree;

                return "{$slice->getHexColor()} {$startDegree}deg {$endDegree}deg";
            })
            ->implode(',');
    }

    protected function getData(): array
    {
        $slices = Collection::make($this->getSlices());

        $totalValue = $slices->sum(function (Slice $slice) {
            return $slice->getValue();
        });

        return $slices->map(function (Slice $slice, int $index) use ($totalValue) {
            $color = $slice->getColor() ?? $this->choices[$index % count($this->choices)];

            $slice->color($this->colors[$color]);
            $slice->hexColor($this->hexColors[$color]);
            $slice->totalValue($totalValue);

            return $slice;
        })->toArray();
    }

    public function render(): View
    {
        return view('filament::widgets.cards.pie-chart.pie-chart-card', $this->data());
    }
}
