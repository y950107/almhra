<?php

namespace App\Filament\Teacher\Resources\RecitationResource\Pages;

use App\Filament\Teacher\Resources\RecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecitation extends EditRecord
{
    protected static string $resource = RecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
