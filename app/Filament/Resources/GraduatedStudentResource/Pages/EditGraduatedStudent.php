<?php

namespace App\Filament\Resources\GraduatedStudentResource\Pages;

use App\Filament\Resources\GraduatedStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGraduatedStudent extends EditRecord
{
    protected static string $resource = GraduatedStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
