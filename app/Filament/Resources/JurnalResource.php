<?php

namespace App\Filament\Resources;

use App\Models\JurnalHeader;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JurnalResource extends Resource
{
    protected static ?string $model = JurnalHeader::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Jurnal';
    protected static ?string $label = 'Jurnal';
    protected static ?string $pluralLabel = 'Jurnal';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Informasi Jurnal')->schema([
                Forms\Components\DatePicker::make('tanggal')->required(),
                Forms\Components\TextInput::make('nomor_referensi')
                    ->label('Nomor Referensi')
                    ->placeholder('Otomatis')
                    ->readonly(),
                Forms\Components\Textarea::make('deskripsi')->rows(3),
            ]),
            \Filament\Schemas\Components\Section::make('Detail Jurnal')->schema([
                Forms\Components\Repeater::make('jurnalDetails')
                    ->relationship('jurnalDetails')
                    ->schema([
                        Forms\Components\Select::make('coa_id')
                            ->relationship('coa', 'nama_akun')
                            ->required(),
                        Forms\Components\TextInput::make('debit')->numeric()->default(0),
                        Forms\Components\TextInput::make('kredit')->numeric()->default(0),
                    ])
                    ->minItems(2)
                    ->addActionLabel('Tambah Baris'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('nomor_referensi')->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50),
            ])
            ->actions([
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
            'index' => \App\Filament\Resources\JurnalResource\Pages\ManageJurnals::route('/'),
        ];
    }
}
