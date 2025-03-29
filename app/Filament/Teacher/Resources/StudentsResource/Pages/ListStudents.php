<?php

namespace App\Filament\Teacher\Resources\StudentsResource\Pages;

use App\Filament\Teacher\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
