<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\AlMaherRecitationResource\Pages;
use App\Filament\Student\Resources\AlMaherRecitationResource\RelationManagers;
use App\Models\AlMaherRecitation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlMaherRecitationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    protected static ?string $slug = "al-maher";

    protected static ?string $model = AlMaherRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->student?->candidate?->program_type === 'mahir';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.almaher-recitation.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.almaher-recitation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.almaher-recitation.plural_model_label');
    }



    public static function table(Table $table): Table
    {

        return $table
            ->query(
                AlMaherRecitation::query()->whereHas('recitationSession.student', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            )
            ->columns([
                TextColumn::make('recitationSession.session_date')
                    ->label('تاريخ الجلسة')
                    ->date('Y-m-d')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('recitationSession.halaka.name')
                    ->label('الحلقة')
                    ->toggleable(),

                TextColumn::make('recitationSession.student.candidate.full_name')
                    ->label('الطالب')
                    ->toggleable(),

                TextColumn::make('recitationSession.present')
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



                TextColumn::make('surah_name')
                    ->label('سورة النهاية')
                    ->toggleable(),

                TextColumn::make('end_ayah_id')
                    ->label('آية النهاية'),

                TextColumn::make('pages')
                    ->label('عدد الاوجه'),

                TextColumn::make('lesson_title')
                    ->label('المتن')
                    ->formatStateUsing(fn($state) => AlMaherRecitation::getLessonTitles()[$state])
                    ->toggleable(),

                TextColumn::make('mem_lines')
                    ->label('حفظ المتن'),

                TextColumn::make('recitationSession.evaluationScore')
                    ->label('التقييم')
                    ->badge()
                    ->color('info')

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
            'index' => Pages\ListRecitations::route('/'),
            'create' => Pages\CreateRecitation::route('/create'),
            'edit' => Pages\EditRecitation::route('/{record}/edit'),
        ];
    }
}
