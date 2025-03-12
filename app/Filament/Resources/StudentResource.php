<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('teacher_id')
                    ->label('المشرف')
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
                    ->label('الاسم'),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('المشرف'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->date(),
                Tables\Columns\TextColumn::make('current_level')
                    ->label('المستوى')
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
