<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\CandidateStatus;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class StudentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'icon-students';

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    public static function getNavigationLabel(): string
    {
        return __('filament.student.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.student.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.student.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->label('الشيخ')
                    ->relationship('teacher', 'name')
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->required(),

                Forms\Components\Section::make('التقدم الدراسي')
                    ->schema([
                        Forms\Components\TextInput::make('current_level')
                            ->label('المستوى الحالي')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\TextInput::make('total_pages')
                            ->label('الصفحات المحفوظة')
                            ->numeric()
                            ->disabled()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('candidate.birthdate')
                    ->label('تاريخ الازدياد ')
                    ->date('Y-m-d'),

                    Tables\Columns\TextColumn::make('candidate.email')
                    ->label('البريد الالكتروني')->sortable()->searchable(),

                    Tables\Columns\IconColumn::make('candidate.has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),

                

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('الشيح')->sortable()->searchable()->badge()->color('success'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->date('Y-m-d'),



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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
