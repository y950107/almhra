<?php

namespace App\Filament\Teacher\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Evaluation;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Teacher\Resources\EvaluationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Teacher\Resources\EvaluationResource\RelationManagers;

class EvaluationResource extends Resource  implements HasShieldPermissions
{
    protected static ?string $model = Evaluation::class;
    protected static ?string $navigationIcon = 'icon-evaluation';

    
    public static function getNavigationLabel(): string
    {
        return __('filament.evaluations.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.evaluations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.evaluations.plural_model_label');
    }
    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('pending_evaluations_count', 60, function () {
            return (string) Evaluation::where('status', 'pending')->count();
        });
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
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
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
