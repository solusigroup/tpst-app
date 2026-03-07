<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class CompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected string $view = 'filament.pages.company-settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Pengaturan Perusahaan';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = auth()->user()->tenant;
        
        if ($tenant) {
            $this->data = $tenant->toArray();
        }
    }

    public function form($form)
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Identitas Perusahaan')
                    ->description('Nama dan alamat resmi perusahaan')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nama Perusahaan')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('Email Perusahaan')
                            ->email(),
                    ])->columns(1),

                \Filament\Schemas\Components\Section::make('Informasi Rekening Bank')
                    ->description('Detail rekening untuk pembayaran invoice')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('bank_name')
                            ->label('Nama Bank'),
                        \Filament\Forms\Components\TextInput::make('bank_account_number')
                            ->label('Nomor Rekening'),
                        \Filament\Forms\Components\TextInput::make('bank_account_name')
                            ->label('Nama Pemilik Rekening'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Pejabat & Otorisasi')
                    ->description('Nama pejabat yang akan muncul di tanda tangan dokumen')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('director_name')
                            ->label('Nama Direktur'),
                        \Filament\Forms\Components\TextInput::make('manager_name')
                            ->label('Nama Manajer'),
                        \Filament\Forms\Components\TextInput::make('finance_name')
                            ->label('Bagian Keuangan/Accounting'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $tenant = auth()->user()->tenant;

        if ($tenant) {
            $tenant->update($data);

            Notification::make()
                ->success()
                ->title('Pengaturan berhasil disimpan!')
                ->send();
        } else {
             Notification::make()
                ->danger()
                ->title('Gagal menyimpan: Tenant tidak ditemukan.')
                ->send();
        }
    }
}
