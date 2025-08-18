<?php

namespace App\Filament\Resources\GraduatedStudentResource\Pages;

use App\Filament\Resources\GraduatedStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGraduatedStudents extends ListRecords
{
    protected static string $resource = GraduatedStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
