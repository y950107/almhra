<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\StudentsResource\Pages;
use App\Filament\Teacher\Resources\StudentsResource\RelationManagers;
use App\Models\Candidate;
use App\Models\Student;
use App\Models\Students;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;

class StudentsResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'icon-students';

    protected static ?int $navigationSort = 2;

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any'];
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




    public static function table(Table $table): Table
    {
        return $table
        ->query(
            Student::query()->where('teacher_id',auth()->user()->teacher->id)->whereHas('halakas', function($q) {
                $q->whereNull('halaka_student.moved_at'); // Pivot table condition
            })
        )
        ->columns([

            Tables\Columns\TextColumn::make('user.name')
                ->label('الاسم')->sortable()->searchable(),

            Tables\Columns\TextColumn::make('user.email')
                ->label('البريد الالكتروني')->sortable()->searchable(),

            Tables\Columns\TextColumn::make('candidate.program_type')
                ->formatStateUsing(fn($state) => Candidate::getProgramTypes()[$state] ?? 'غير معروف')
                ->label('البرنامج')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('info'),


            Tables\Columns\TextColumn::make('present_sessions_percentage')
                ->label('التسميع الحضوري')
                ->suffix('%'),

            Tables\Columns\TextColumn::make('online_sessions_percentage')
                ->label('التسميع عن بعد')
                ->suffix('%'),


                    Tables\Columns\IconColumn::make('candidate.has_ijaza')
                    ->label('لديه إجازة')
                    ->boolean()->sortable()->toggleable(),


                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ الالتحاق')
                    ->badge()
                    ->color('success')
                    ->date('Y-m-d'),


            CircleProgress::make('progress_percentage')->label('نسبة الإنجاز')
                ->getStateUsing(function ($record) {
                    $total = 100;
                    $progress = $record->getProgressPercentageAttribute();
                    return [
                        'total' => $total,
                        'progress' => $progress,
                    ];
                }),
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
        //    'create' => Pages\CreateStudents::route('/create'),
          //  'edit' => Pages\EditStudents::route('/{record}/edit'),
        ];
    }
}
