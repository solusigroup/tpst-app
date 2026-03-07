<?php

namespace App\Filament\Resources;

use App\Models\HasilPilahan;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HasilPilahanResource extends Resource
{
    protected static ?string $model = HasilPilahan::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationLabel = 'Hasil Pilahan';
    protected static ?string $label = 'Hasil Pilahan Sampah';
    protected static ?string $pluralLabel = 'Hasil Pilahan Sampah';

    public static function getNavigationGroup(): string | null
    {
        return 'Operasional';
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Data Hasil Pilahan')->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('kategori')
                    ->options([
                        'Organik' => 'Organik',
                        'Anorganik' => 'Anorganik',
                        'B3' => 'B3 (Bahan Berbahaya & Beracun)',
                        'Residu' => 'Residu',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('jenis')
                    ->required()
                    ->placeholder('cth: Plastik, Kertas, Logam, Kompos, dll.')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tonase')
                    ->label('Tonase (kg)')
                    ->numeric()
                    ->required()
                    ->suffix('kg'),
                Forms\Components\TextInput::make('officer')
                    ->label('Petugas')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('keterangan')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Organik' => 'success',
                        'Anorganik' => 'info',
                        'B3' => 'danger',
                        'Residu' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tonase')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('officer')
                    ->label('Petugas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')->options([
                    'Organik' => 'Organik',
                    'Anorganik' => 'Anorganik',
                    'B3' => 'B3',
                    'Residu' => 'Residu',
                ]),
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
            'index' => \App\Filament\Resources\HasilPilahanResource\Pages\ManageHasilPilahans::route('/'),
        ];
    }
}
