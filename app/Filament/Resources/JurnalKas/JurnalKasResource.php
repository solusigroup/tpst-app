<?php

namespace App\Filament\Resources\JurnalKas;

use App\Filament\Resources\JurnalKas\Pages\CreateJurnalKas;
use App\Filament\Resources\JurnalKas\Pages\EditJurnalKas;
use App\Filament\Resources\JurnalKas\Pages\ListJurnalKas;
use App\Filament\Resources\JurnalKas\Schemas\JurnalKasForm;
use App\Filament\Resources\JurnalKas\Tables\JurnalKasTable;
use App\Models\JurnalKas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JurnalKasResource extends Resource
{
    protected static ?string $model = JurnalKas::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Jurnal Kas';
    protected static ?string $label = 'Jurnal Kas';
    protected static ?string $pluralLabel = 'Jurnal Kas';
    
    // Add navigation group to match standard Jurnal if needed
    // protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $recordTitleAttribute = 'deskripsi';

    public static function form(Schema $schema): Schema
    {
        return JurnalKasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JurnalKasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJurnalKas::route('/'),
            'create' => CreateJurnalKas::route('/create'),
            'edit' => EditJurnalKas::route('/{record}/edit'),
        ];
    }
}
