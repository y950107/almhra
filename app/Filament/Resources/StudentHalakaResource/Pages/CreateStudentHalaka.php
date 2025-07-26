<?php

namespace App\Filament\Resources\StudentHalakaResource\Pages;

use App\Filament\Resources\StudentHalakaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentHalaka extends CreateRecord
{
    protected static string $resource = StudentHalakaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): string
    {
        return __('filament.student_halaka.created_notification_title');
    }
}
