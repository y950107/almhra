<?php

namespace App\Filament\Resources\StudentHalakaResource\Pages;

use App\Filament\Resources\StudentHalakaResource;
use Filament\Resources\Pages\EditRecord;

class EditStudentHalaka extends EditRecord
{
    protected static string $resource = StudentHalakaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getUpdatedNotificationTitle(): string
    {
        return __('filament.student_halaka.updated_notification_title');
    }
}
