<?php

namespace App\Filament\Teacher\Resources\EvaluationsResource\Pages;

use App\Filament\Teacher\Resources\EvaluationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluations extends EditRecord
{
    protected static string $resource = EvaluationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
