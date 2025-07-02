<?php

namespace App\Filament\Resources\AlMaqraaRecitationResource\Pages;

use App\Filament\Resources\AlMaqraaRecitationResource;
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
        // Move the fields into recitationSession
        $data['recitationSession'] = array_merge($data['recitationSession'], [
                'tajweed_score' => $data['tajweed_score'] ?? 0,
                'fluency_score' => $data['fluency_score'] ?? 0,
                'memory_score' => $data['memory_score'] ?? 0,
                'evaluation_notes' => $data['evaluation_notes'] ?? '',
                'notes' => $data['notes'] ?? ''
            ]);
            
            // Remove the original fields
            unset(
                $data['tajweed_score'],
                $data['fluency_score'],
                $data['memory_score'],
                $data['evaluation_notes'],
                $data['notes']
            );
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
