<?php

namespace App\Filament\Student\Widgets;

use App\Models\RecitationSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LastRecitaionWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;
    protected static ?string $heading = 'آخر جلسة تسميع';

    public function table(Table $table): Table
    {
        return $table
    ->query(
        fn () => RecitationSession::whereHas('student', function ($query) {
            $query->where('user_id', auth()->id());
        })

        ->orderBy('session_date', 'desc')
        ->latest()
    )
    ->columns([
        TextColumn::make('session_date')
            ->label('تاريخ الجلسة')
            ->date('Y-m-d')
            ->badge()
            ->color('info'),

        TextColumn::make('halaka.teacher.name')
            ->label('المعلم')
            ->toggleable(),


        TextColumn::make('present')
            ->label('الحضور')
            ->badge()
            ->formatStateUsing(fn ($state): string => match ($state) {
                'present' => 'حاضر',
                'absent_with_excuse' => 'غائب بعذر',
                'absent_without_excuse' => 'غائب بدون عذر',
                default => 'غير معروف',
            })
            ->color(fn ($state): string => match ($state) {
                'present' => 'success',
                'absent_with_excuse' => 'warning',
                'absent_without_excuse' => 'danger',
                default => 'secondary',
            }),

    ])->emptyStateHeading('لا توجد جلسات حالياً')
    ->emptyStateDescription('لم يتم تسجيل أي جلسة تابعة لك بعد.');

    }

}
