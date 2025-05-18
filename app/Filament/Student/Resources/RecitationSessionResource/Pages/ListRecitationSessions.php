<?php

namespace App\Filament\Student\Resources\RecitationSessionResource\Pages;

use App\Filament\Student\Resources\RecitationSessionResource;
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
