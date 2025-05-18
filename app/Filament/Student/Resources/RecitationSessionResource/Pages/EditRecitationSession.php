<?php

namespace App\Filament\Student\Resources\RecitationSessionResource\Pages;

use App\Filament\Student\Resources\RecitationSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecitationSession extends EditRecord
{
    protected static string $resource = RecitationSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
