<?php

namespace App\Filament\Teacher\Resources\EvaluationsResource\Pages;

use App\Filament\Teacher\Resources\EvaluationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
