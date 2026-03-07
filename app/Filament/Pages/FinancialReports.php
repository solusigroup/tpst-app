<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;

class FinancialReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $title = 'Laporan Keuangan';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.financial-reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'month' => date('m'),
            'year' => date('Y'),
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                Section::make('Filter Periode Laporan')
                    ->description('Pilih bulan dan tahun untuk menghasilkan laporan keuangan')
                    ->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->required()
                            ->native(false),
                        Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2023), range(date('Y'), 2023)))
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetakLabaRugiHeader')
                ->label('Preview Laba Rugi')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('reports.laba-rugi', [
                    'month' => $this->data['month'],
                    'year' => $this->data['year'],
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
