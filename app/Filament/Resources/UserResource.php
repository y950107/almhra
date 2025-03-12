<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\UserResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class UserResource extends Resource 
{
   
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'icon-users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('الاسم')
                ->required(),
            TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required(),
            TextInput::make('phone')
                ->label('الهاتف')
                ->required(),
            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->dehydrateStateUsing(fn ($state, $record) => filled($state) ? bcrypt($state) : $record->password)
                ->required(fn($context) => $context === 'create'),
            Select::make('type')
                ->label('نوع المستخدم')
                ->options([
                    'admin' => 'مدير',
                    'teacher' => 'معلم',
                    'student' => 'طالب',
                ])
                ->required(),
                Toggle::make('account_status')
                ->label('حالة الحساب')
                ->default(true)
                ->reactive(),
            Select::make('roles')
                ->label('الدور')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('المعرف'),
            TextColumn::make('name')->label('الاسم')->searchable(),
            TextColumn::make('email')->label('البريد الإلكتروني')->searchable(),
            TextColumn::make('phone')->label('الهاتف')->searchable(),
            TextColumn::make('type')->label('النوع'),
            TextColumn::make('roles.name')
                ->label('الدور')
                ->badge()
                ->color('primary'),
            TextColumn::make('account_status')
                ->label('حالة الحساب')
                ->formatStateUsing(fn($state) => $state ? 'مفعل' : 'غير مفعل')
                ->badge()
                ->colors([
                    'success' => fn($state) => $state ? 'green' : 'red', ]),
            TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
