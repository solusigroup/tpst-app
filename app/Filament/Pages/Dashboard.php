<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\DailyTonnageChart;
use App\Filament\Widgets\RevenueChart;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            DailyTonnageChart::class,
            RevenueChart::class,
        ];
    }
}
