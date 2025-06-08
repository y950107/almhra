<?php

namespace App\Filament\Student\Resources\AlMaqraaRecitationResource\Pages;

use App\Filament\Student\Resources\AlMaqraaRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecitation extends EditRecord
{
    protected static string $resource = AlMaqraaRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return AlMaqraaRecitationResource::getUrl();
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
            ];
        }

        return parent::mutateFormDataBeforeSave($data);

    }
}
