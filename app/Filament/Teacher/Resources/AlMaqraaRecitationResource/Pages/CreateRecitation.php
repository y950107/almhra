<?php

namespace App\Filament\Teacher\Resources\AlMaqraaRecitationResource\Pages;

use App\Filament\Teacher\Resources\AlMaqraaRecitationResource;
use App\Models\AlMaqraaRecitation;
use App\Models\RecitationSession;
use Filament\Resources\Pages\CreateRecord;

class CreateRecitation extends CreateRecord
{
    protected static string $resource = AlMaqraaRecitationResource::class;

    protected function getRedirectUrl(): string
    {
        return AlMaqraaRecitationResource::getUrl();
    }


    protected function handleRecordCreation(array $data): AlMaqraaRecitation
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
