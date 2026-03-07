<?php

namespace App\Filament\Resources;

use App\Models\Ritase;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RitaseResource extends Resource
{
    protected static ?string $model = Ritase::class;
    protected static ?string $navigationLabel = 'Ritase';
    protected static ?string $label = 'Ritase';
    protected static ?string $pluralLabel = 'Ritase';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Informasi Ritase')->schema([
                Forms\Components\TextInput::make('nomor_tiket')
                    ->label('Nomor Tiket')
                    ->placeholder('Otomatis')
                    ->readonly(),
                Forms\Components\Select::make('armada_id')->relationship('armada', 'plat_nomor')->required(),
                Forms\Components\Select::make('klien_id')->relationship('klien', 'nama_klien')->required(),
                Forms\Components\DateTimePicker::make('waktu_masuk')->required(),
                Forms\Components\DateTimePicker::make('waktu_keluar'),
            ]),
            \Filament\Schemas\Components\Section::make('Pengukuran Berat')->schema([
                Forms\Components\TextInput::make('berat_bruto')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('berat_netto', ($get('berat_bruto') ?? 0) - ($get('berat_tarra') ?? 0))),
                Forms\Components\TextInput::make('berat_tarra')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get) => $set('berat_netto', ($get('berat_bruto') ?? 0) - ($get('berat_tarra') ?? 0))),
                Forms\Components\TextInput::make('berat_netto')
                    ->numeric()
                    ->readonly()
                    ->dehydrated(),
            ]),
            \Filament\Schemas\Components\Section::make('Detail')->schema([
                Forms\Components\TextInput::make('jenis_sampah'),
                Forms\Components\TextInput::make('biaya_tipping')->numeric(),
                Forms\Components\Select::make('status')->options([
                    'masuk' => 'Masuk',
                    'timbang' => 'Timbang',
                    'keluar' => 'Keluar',
                    'selesai' => 'Selesai',
                ])->default('masuk')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tiket')->searchable(),
                Tables\Columns\TextColumn::make('armada.plat_nomor'),
                Tables\Columns\TextColumn::make('klien.nama_klien'),
                Tables\Columns\TextColumn::make('berat_netto')->numeric(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                \Filament\Actions\Action::make('invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->modalHeading('Cetak Invoice Ritase')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(fn (Ritase $record) => view('ritase.invoice', ['ritase' => $record])),
                \Filament\Actions\Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function (Ritase $record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ritase.invoice', [
                            'ritase' => $record,
                            'isExport' => true,
                        ]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "Invoice_Ritase_{$record->nomor_tiket}.pdf"
                        );
                    }),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\RitaseResource\Pages\ManageRitases::route('/'),
        ];
    }
}
