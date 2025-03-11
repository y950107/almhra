<?php

namespace App\Filament\Resources\SessionbatchResource\Pages;

use App\Filament\Resources\SessionbatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessionbatches extends ListRecords
{
    protected static string $resource = SessionbatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
