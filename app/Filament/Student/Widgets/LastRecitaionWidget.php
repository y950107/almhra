<?php

namespace App\Filament\Student\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\RecitationSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LastRecitaionWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;
    protected static ?string $heading = 'آخر جلست التسميع';
  
    public function table(Table $table): Table
    {
        return $table
        
            ->query(
                RecitationSession::whereHas('student', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                    ->whereDate('session_date', '>=', now())
                    ->orderBy('session_date', 'desc')
                    ->latest()
            )
            ->columns([
                TextColumn::make('session_date')->label('تاريخ الجلسة')->date('Y-m-d')->toggleable(),
                TextColumn::make('halaka.name')->label('الحلقة')->toggleable(),
                TextColumn::make('student.teacher.name')->label('المعلم')->toggleable(),
                TextColumn::make('start_surah_name')->label('سورة البداية')->toggleable(),
                TextColumn::make('start_ayah_text')->label('آية البداية')->limit(30)->toggleable(),
                TextColumn::make('end_surah_name')->label('سورة النهاية')->toggleable(),
                TextColumn::make('end_ayah_text')->label('آية النهاية')->limit(30)->toggleable(),
                TextColumn::make('actual_end_ayah_text')->label('آية محققة')->limit(30)->toggleable(),
                TextColumn::make('target_percentage')
                    ->label('المستهدف')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->colors(['success'])
                    ->toggleable(),

            ]);
    }
   
}
