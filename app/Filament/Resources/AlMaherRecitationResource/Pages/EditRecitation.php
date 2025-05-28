<?php

namespace App\Filament\Resources\AlMaherRecitationResource\Pages;

use App\Filament\Resources\AlMaherRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecitation extends EditRecord
{
    protected static string $resource = AlMaherRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return AlMaherRecitationResource::getUrl();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if ($data['recitationSession']['present'] !== 'present') {
            $data = [
                'recitation_session_id' => $data['recitationSession']['id'],
                'start_surah_id' => null,
                'end_surah_id' => null,
                'start_ayah_id' => null,
                'end_ayah_id' => null,
                'pages' => null,
                'lesson_title' => null,
                'mem_lines' =>  null,
            ];
        }

        return parent::mutateFormDataBeforeSave($data);

    }
}
