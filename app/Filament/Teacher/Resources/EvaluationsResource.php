<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Resources\EvaluationResource;
use App\Filament\Teacher\Resources\EvaluationsResource\Pages;
use App\Filament\Teacher\Resources\EvaluationsResource\RelationManagers;
use App\Models\Evaluation;
use App\Models\Evaluations;
use Filament\Tables\Table;

class EvaluationsResource extends EvaluationResource
{
    protected static ?string $model = Evaluation::class;
    protected static ?string $navigationIcon = 'icon-evaluation';

    protected static ?int $navigationSort = 6;


    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('pending_evaluations_count', 60, function () {
            return (string) Evaluation::query()->whereHas('candidate.teacher.user', function ($query) {
                $query->where('id', auth()->id());
            })->where('status', 'pending')->count();
        });
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }



    public static function table(Table $table): Table
    {

        $table = parent::table($table);

        return $table
            ->query(Evaluation::query()->whereHas('candidate.teacher.user', function ($query) {
                $query->where('id', auth()->id());
            }));
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluations::route('/create'),
            'edit' => Pages\EditEvaluations::route('/{record}/edit'),
        ];
    }
}
