<?php

namespace App\Filament\Resources\StudentHalakaResource\Pages;

use App\Filament\Resources\StudentHalakaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentHalakas extends ListRecords
{
    protected static string $resource = StudentHalakaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
