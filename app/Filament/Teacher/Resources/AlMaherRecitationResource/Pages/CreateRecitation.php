<?php

namespace App\Filament\Teacher\Resources\AlMaherRecitationResource\Pages;

use App\Filament\Teacher\Resources\AlMaherRecitationResource;
use App\Models\AlMaherRecitation;
use App\Models\RecitationSession;
use Filament\Resources\Pages\CreateRecord;

class CreateRecitation extends CreateRecord
{
    protected static string $resource = AlMaherRecitationResource::class;

    protected function getRedirectUrl(): string
    {
        return AlMaherRecitationResource::getUrl();
    }

    protected function handleRecordCreation(array $data): AlMaherRecitation
    {
        $recitationSession = RecitationSession::create($data['recitationSession']);

        $data['recitation_session_id'] = $recitationSession->id;

        if ($data['recitationSession']['present'] !== 'present') {
            $data = [
                'recitation_session_id' => $recitationSession->id,
            ];
        }

        return static::getModel()::create($data);
    }
}
