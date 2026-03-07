<?php

namespace App\Filament\Central\Resources;

use App\Models\Tenant;
use App\Services\TenantProvisioningService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Tenant';

    protected static ?string $modelLabel = 'Tenant';

    protected static ?string $pluralModelLabel = 'Tenants';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Tenant')
                    ->description('Informasi dasar tenant')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tenant')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('PT Sampah Jaya'),
                        Forms\Components\TextInput::make('domain')
                            ->label('Domain')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('sampahjaya.test')
                            ->helperText('Domain unik untuk tenant ini'),
                    ])->columns(2),

                Forms\Components\Section::make('Admin Tenant')
                    ->description('Buat admin user untuk tenant baru ini')
                    ->schema([
                        Forms\Components\TextInput::make('admin_name')
                            ->label('Nama Admin')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Admin TPST'),
                        Forms\Components\TextInput::make('admin_username')
                            ->label('Username Admin')
                            ->maxLength(255)
                            ->placeholder('admin_tpst'),
                        Forms\Components\TextInput::make('admin_email')
                            ->label('Email Admin')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('admin@sampahjaya.test'),
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Password Admin')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->revealable()
                            ->placeholder('Minimal 8 karakter'),
                    ])->columns(2)
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Semua data tenant (users, klien, armada, ritase, dll) akan dihapus permanen. Apakah Anda yakin?'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => \App\Filament\Central\Resources\TenantResource\Pages\ListTenants::route('/'),
            'create' => \App\Filament\Central\Resources\TenantResource\Pages\CreateTenant::route('/create'),
            'edit' => \App\Filament\Central\Resources\TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
