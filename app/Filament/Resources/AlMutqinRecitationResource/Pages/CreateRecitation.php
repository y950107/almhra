<?php

namespace App\Filament\Resources\AlMutqinRecitationResource\Pages;

use App\Filament\Resources\AlMutqinRecitationResource;
use App\Models\AlMutqinRecitation;
use App\Models\RecitationSession;
use Filament\Resources\Pages\CreateRecord;

class CreateRecitation extends CreateRecord
{
    protected static string $resource = AlMutqinRecitationResource::class;

    protected function getRedirectUrl(): string
    {
        return AlMutqinRecitationResource::getUrl();
    }

    protected function handleRecordCreation(array $data): AlMutqinRecitation
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
