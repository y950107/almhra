<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\AlMutqinRecitationResource\Pages;
use App\Filament\Student\Resources\AlMutqinRecitationResource\RelationManagers;
use App\Models\AlMutqinRecitation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlMutqinRecitationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    protected static ?string $slug = "al-mutqin";

    protected static ?string $model = AlMutqinRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';


    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->student?->candidate?->program_type === 'mutqin';
    }

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('filament.almutqin-recitation.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.almutqin-recitation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.almutqin-recitation.navigation_label');
    }



    public static function table(Table $table): Table
    {

        return $table
            ->query(
                AlMutqinRecitation::query()->whereHas('recitationSession.student', function ($query) {
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


                TextColumn::make('mem_surah_name')
                    ->label('سورة النهاية (الحفظ)')
                    ->toggleable(),

                TextColumn::make('mem_pages')
                    ->label('عدد اوجه الحفظ')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('rev_surah_name')
                    ->label('سورة النهاية (المراجعة)')
                    ->toggleable(),

                TextColumn::make('rev_pages')
                    ->label('عدد اوجه المراجعة')
                    ->limit(30)
                    ->toggleable(),

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
        ];
    }
}
