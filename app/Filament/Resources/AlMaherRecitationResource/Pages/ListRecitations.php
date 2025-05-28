<?php

namespace App\Filament\Resources\AlMaherRecitationResource\Pages;

use App\Filament\Resources\AlMaherRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecitations extends ListRecords
{
    protected static string $resource = AlMaherRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
