<?php

namespace App\Filament\Resources\JurnalKas\Schemas;

use Filament\Schemas\Schema;

class JurnalKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Transaksi')->schema([
                    \Filament\Forms\Components\Select::make('tipe')
                        ->options([
                            'Penerimaan' => 'Penerimaan',
                            'Pengeluaran' => 'Pengeluaran',
                        ])
                        ->required()
                        ->live(),
                    \Filament\Forms\Components\DatePicker::make('tanggal')
                        ->required()
                        ->default(now()),
                    \Filament\Forms\Components\Select::make('coa_kas_id')
                        ->label('Akun Kas/Bank')
                        ->relationship('coaKas', 'nama_akun', function (\Illuminate\Database\Eloquent\Builder $query) {
                            $query->where('klasifikasi', 'Kas & Bank')->orWhere('nama_akun', 'like', '%Kas%')->orWhere('nama_akun', 'like', '%Bank%');
                        })
                        ->required()
                        ->searchable(),
                    \Filament\Forms\Components\Select::make('coa_lawan_id')
                        ->label('Akun Lawan (Pendapatan/Biaya)')
                        ->relationship('coaLawan', 'nama_akun')
                        ->required()
                        ->searchable(),
                    \Filament\Forms\Components\TextInput::make('nominal')
                        ->required()
                        ->numeric()
                        ->default(0),
                    \Filament\Forms\Components\Textarea::make('deskripsi')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),
                \Filament\Schemas\Components\Section::make('Bukti Transaksi')->schema([
                    \Filament\Forms\Components\FileUpload::make('bukti_transaksi')
                        ->image()
                        ->imageEditor() // Optional: allows minor cropping/rotating
                        ->acceptedFileTypes(['image/*']) // Ensures mobile browsers show Camera & Gallery options
                        ->maxSize(5120) // 5MB limit
                        ->directory('bukti-transaksi-kas')
                        ->helperText('Bisa menggunakan foto langsung dari Kamera atau pilih dari Galeri.')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
