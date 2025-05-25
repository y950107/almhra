<?php

namespace App\Filament\Teacher\Resources\AlMaqraaRecitationResource\Pages;

use App\Filament\Teacher\Resources\AlMaqraaRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecitations extends ListRecords
{
    protected static string $resource = AlMaqraaRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
