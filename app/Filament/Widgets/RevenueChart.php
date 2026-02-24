<?php

namespace App\Filament\Widgets;

use App\Models\Ritase;
use App\Models\Penjualan;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Revenue';

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(function ($month) {
            $date = Carbon::now()->subMonths($month);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();

            $tipping = Ritase::whereBetween('waktu_masuk', [$startDate, $endDate])->sum('biaya_tipping');
            $sales = Penjualan::whereBetween('tanggal', [$startDate, $endDate])->sum('total_harga');

            return [
                'month' => $date->format('M Y'),
                'tipping' => $tipping,
                'sales' => $sales,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Tipping Revenue',
                    'data' => $months->pluck('tipping')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Sales Revenue',
                    'data' => $months->pluck('sales')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
