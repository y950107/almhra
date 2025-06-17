<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\AlMaqraaRecitationResource\Pages;
use App\Filament\Student\Resources\AlMaqraaRecitationResource\RelationManagers;
use App\Models\AlMaqraaRecitation;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlMaqraaRecitationResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    protected static ?string $slug = "al-maqraa";

    protected static ?string $model = AlMaqraaRecitation::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->student?->candidate?->program_type === 'maqraa';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.almaqraa-recitation.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.almaqraa-recitation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.almaqraa-recitation.navigation_label');
    }


    public static function table(Table $table): Table
    {

        return $table
            ->query(
                AlMaqraaRecitation::query()->whereHas('recitationSession.student', function ($query) {
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

                TextColumn::make('ayah_text')
                    ->label('آية النهاية')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('pages')
                    ->label('عدد الاوجه')
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
            'create' => Pages\CreateRecitation::route('/create'),
            'edit' => Pages\EditRecitation::route('/{record}/edit'),
        ];
    }
}
