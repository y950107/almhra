<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class StudentRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidate.full_name')->label('اسم الطالب')->sortable()
           
            ])
            // ->filters([
            //     Tables\Filters\Filter::make('memorization_percentage')
            //         ->query(fn (Builder $query) => $query->where('memorization_percentage', '>=', 50))
            //         ->label('طلاب الحفظ فوق 50%'),
            // ])
            ->defaultSort('candidate.full_name');
    }
}
