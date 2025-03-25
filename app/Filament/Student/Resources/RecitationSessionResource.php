<?php

namespace App\Filament\Student\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RecitationSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Student\Resources\RecitationSessionResource\Pages;
use App\Filament\Student\Resources\RecitationSessionResource\RelationManagers;

class RecitationSessionResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }
    protected static ?string $model = RecitationSession::class;

    protected static ?string $navigationIcon = 'icon-recitations';

    public static function getNavigationLabel(): string
    {
        return __('filament.recitationsesion.navigation_label');
    }
    public static function getModelLabel(): string
    {
        return __('filament.recitationsesion.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.recitationsesion.plural_model_label');
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
                TextColumn::make('student.candidate.full_name')->label('الطالب')->toggleable(),
                TextColumn::make('start_surah_name')->label('سورة البداية')->toggleable(),
                TextColumn::make('start_ayah_text')->label('آية البداية')->limit(30)->toggleable(),
                TextColumn::make('start_page')->label('صفحة البداية')->toggleable(),
                TextColumn::make('end_surah_name')->label('سورة النهاية')->toggleable(),
                TextColumn::make('end_ayah_text')->label('آية النهاية')->limit(30)->toggleable(),
                TextColumn::make('end_page')->label('صفحة النهاية')->toggleable(),
                TextColumn::make('actual_end_ayah_text')->label('آية محققة')->limit(30)->toggleable(),
                TextColumn::make('actual_end_page')->label('صفحة المحققة')->toggleable(),
                TextColumn::make('target_percentage')
                    ->label('المستهدف')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->colors(['success'])
                    ->toggleable(),
                TextColumn::make('achievement_percentage')
                    ->label('المحقق')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state >= 80,
                        'warning' => fn($state) => $state < 80 && $state >= 50,
                        'danger'  => fn($state) => $state < 50
                    ])
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('halaka_id')->label('الحلقة')->relationship('halaka', 'name'),
                
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
            'index' => Pages\ListRecitationSessions::route('/'),
            'create' => Pages\CreateRecitationSession::route('/create'),
            'edit' => Pages\EditRecitationSession::route('/{record}/edit'),
        ];
    }
}
