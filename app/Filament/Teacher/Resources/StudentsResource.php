<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\StudentsResource\Pages;
use App\Filament\Teacher\Resources\StudentsResource\RelationManagers;
use App\Models\Student;
use App\Models\Students;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'icon-students';

    protected static ?int $navigationSort = 2;

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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(
            Student::whereHas('teacher.user', function ($query) {
                return $query->where('id', auth()->id());
            })
        )
        ->columns([
            Tables\Columns\TextColumn::make('user.name')
                    ->label('الاسم')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('candidate.birthdate')
                    ->label('تاريخ الازدياد ')
                    ->date('Y-m-d'),

                    Tables\Columns\TextColumn::make('candidate.email')
                    ->label('الايمل')->sortable()->searchable(),

                    Tables\Columns\IconColumn::make('candidate.has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),

                

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('المشرف')->sortable()->searchable()->badge()->color('success'),
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
            'create' => Pages\CreateStudents::route('/create'),
            'edit' => Pages\EditStudents::route('/{record}/edit'),
        ];
    }
}
