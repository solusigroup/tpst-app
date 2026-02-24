<?php

namespace App\Filament\Widgets;

use App\Models\Ritase;
use App\Models\Penjualan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        return [
            Stat::make('Tonase Hari Ini (kg)', $this->getTonaseHariIni())
                ->description('Total berat netto masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pendapatan Tipping Hari Ini', $this->formatCurrency($this->getPendapatanTippingHariIni()))
                ->description('Total biaya tipping hari ini')
                ->descriptionIcon('heroicon-m-banknote')
                ->color('info'),

            Stat::make('Total Penjualan Bulan Ini', $this->formatCurrency($this->getPenjualanBulanIni()))
                ->description('Total revenue penjualan bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }

    protected function getTonaseHariIni(): string
    {
        $total = Ritase::whereDate('waktu_masuk', Carbon::today())->sum('berat_netto');
        return number_format($total, 2, ',', '.');
    }

    protected function getPendapatanTippingHariIni(): float
    {
        return Ritase::whereDate('waktu_masuk', Carbon::today())
            ->where('biaya_tipping', '>', 0)
            ->sum('biaya_tipping');
    }

    protected function getPenjualanBulanIni(): float
    {
        return Penjualan::whereBetween('tanggal', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('total_harga');
    }

    protected function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
