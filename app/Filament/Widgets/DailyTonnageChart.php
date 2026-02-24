<?php

namespace App\Filament\Widgets;

use App\Models\Ritase;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class DailyTonnageChart extends ChartWidget
{
    protected ?string $heading = 'Daily Waste Input (Last 7 Days)';

    protected function getData(): array
    {
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = Carbon::today()->subDays($day);
            $total = Ritase::whereDate('waktu_masuk', $date)->sum('berat_netto');
            return [
                'date' => $date->format('D, M d'),
                'total' => $total,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Tonnage (kg)',
                    'data' => $last7Days->pluck('total')->toArray(),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $last7Days->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return value + " kg"; }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}
