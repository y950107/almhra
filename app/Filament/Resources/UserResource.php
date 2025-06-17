<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'icon-users';

    public static function getNavigationLabel(): string
    {
        return __('filament.user.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.user.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.user.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
                Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                    ->label('الاسم')
                    ->required(),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->unique(ignoreRecord: true)
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->confirmed()
                        ->required(fn(string $context) => $context !== 'edit')
                        ->maxLength(255)
                        ->label('كلمة المرور'),

                    TextInput::make('password_confirmation')
                        ->password()
                        ->revealable()
                        ->required(fn(string $context) => $context !== 'edit')
                        ->maxLength(255)
                        ->label('تاكيد كلمة المرور'),

                    TextInput::make('phone')
                        ->label('الهاتف')
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Select::make('type')
                        ->label('نوع المستخدم')
                        ->columnStart(1)
                        ->options([
                            'admin' => 'مدير',
                            'teacher' => 'معلم',
                            'student' => 'طالب',
                        ])
                        ->required(),

                    Select::make('roles')
                        ->label('الدور')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->dehydrated()
                        ->required(),

                    Toggle::make('acount_status')
                        ->label('حالة الحساب')
                        ->default(true)
                        ->columnSpanFull()
                        ->reactive(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at','desc')
            ->columns([
            TextColumn::make('id')->label('المعرف'),
            TextColumn::make('name')->label('الاسم')->searchable(),
            TextColumn::make('email')->label('البريد الإلكتروني')->searchable(),
            TextColumn::make('phone')->label('الهاتف')->searchable(),
            TextColumn::make('type')->label('النوع'),
            TextColumn::make('roles.name')
                ->label('الدور')
                ->badge()
                ->color('primary'),
            TextColumn::make('acount_status')
                ->label('حالة الحساب')
                ->formatStateUsing(fn($state) => $state ? 'مفعل' : 'غير مفعل')
                ->badge()
                ->colors([
                    'success' => fn($state) => $state,
                    'danger' => fn($state) => !$state
                ]),
            TextColumn::make('created_at')->label('تاريخ الإنشاء')->date('Y-m-d')
            ->badge()->color('info'),
        ])->actions([
            \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make()
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
