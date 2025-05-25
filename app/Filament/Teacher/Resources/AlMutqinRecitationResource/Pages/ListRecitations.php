<?php

namespace App\Filament\Teacher\Resources\AlMutqinRecitationResource\Pages;

use App\Filament\Teacher\Resources\AlMutqinRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecitations extends ListRecords
{
    protected static string $resource = AlMutqinRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
