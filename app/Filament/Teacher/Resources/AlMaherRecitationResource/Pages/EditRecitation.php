<?php

namespace App\Filament\Teacher\Resources\AlMaherRecitationResource\Pages;

use App\Filament\Teacher\Resources\AlMaherRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\RecitationSession;
use Illuminate\Database\Eloquent\Model;
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
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recitationSession'] = RecitationSession::find($data['recitation_session_id']);
        
      
        $data["tajweed_score"] =  $this->record?->recitationSession?->tajweed_score;
        $data["fluency_score"] =  $this->record?->recitationSession?->fluency_score;
        $data["memory_score"] =  $this->record?->recitationSession?->memory_score;
        $data["evaluation_notes"] =  $this->record?->recitationSession?->evaluation_notes;
        $data["notes"] =  $this->record?->recitationSession?->notes;
        
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
       
        $session = RecitationSession::find($data['recitationSession']['id']);
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
        $data['recitationSession']["evaluation_notes"] =  $data['evaluation_notes'] ?? '';
        $data['recitationSession']["notes"] =  $data['notes'] ?? '';
        $data['recitationSession']["tajweed_score"] =  $data['tajweed_score'] ?? 0;
        $data['recitationSession']["memory_score"] =  $data['memory_score'] ?? 0;
        $data['recitationSession']["fluency_score"] =  $data['fluency_score'] ?? 0;

        $session->update($data['recitationSession']);
        return parent::mutateFormDataBeforeSave($data);

    }
}
