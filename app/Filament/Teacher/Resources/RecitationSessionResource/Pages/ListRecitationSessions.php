<?php

namespace App\Filament\Teacher\Resources\RecitationSessionResource\Pages;

use App\Filament\Teacher\Resources\RecitationSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecitationSessions extends ListRecords
{
    protected static string $resource = RecitationSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
