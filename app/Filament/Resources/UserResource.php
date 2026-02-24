<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $label = 'Users';

    /**
     * Check if current user can access this resource.
     * Only superusers can access user management.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'superuser';
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Section::make('User Information')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->unique(User::class, 'username', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignoreRecord: true),
            ]),
            Forms\Components\Section::make('Security & Role')->schema([
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn(string $context) => $context === 'create')
                    ->nullable(),
                Forms\Components\Select::make('role')
                    ->options([
                        'superuser' => 'Superuser (Full Access)',
                        'admin' => 'Admin',
                        'timbangan' => 'Timbangan (Weighing)',
                        'keuangan' => 'Keuangan (Finance)',
                    ])
                    ->required(),
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->nullable()
                    ->helperText('Leave empty for superuser'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'superuser' => 'danger',
                        'admin' => 'info',
                        'timbangan' => 'warning',
                        'keuangan' => 'success',
                    }),
                Tables\Columns\TextColumn::make('tenant.name'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->options([
                    'superuser' => 'Superuser',
                    'admin' => 'Admin',
                    'timbangan' => 'Timbangan',
                    'keuangan' => 'Keuangan',
                ]),
            ]);
    }
}
