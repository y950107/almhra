<?php

namespace App\Filament\Resources\AlMutqinRecitationResource\Pages;

use App\Filament\Resources\AlMutqinRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\RecitationSession;
use Illuminate\Database\Eloquent\Model;

class EditRecitation extends EditRecord
{
    protected static string $resource = AlMutqinRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['recitationSession'] = RecitationSession::find($data['recitation_session_id']);
        
        $data['tajweed_score'] = $this->record?->recitationSession?->tajweed_score;
        $data['fluency_score'] = $this->record?->recitationSession?->fluency_score;
        $data['memory_score'] = $this->record?->recitationSession?->memory_score;
        $data["evaluation_notes"] =  $this->record?->recitationSession?->evaluation_notes;
        $data["notes"] =  $this->record?->recitationSession?->notes;
        
        return $data;
    }
    protected function getRedirectUrl(): ?string
    {
        return AlMutqinRecitationResource::getUrl();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $session = RecitationSession::find($data['recitationSession']['id']);
        
        if ($data['recitationSession']['present'] !== 'present') {
            $data = [
                'recitation_session_id' => $data['recitationSession']['id'],
                'mem_start_surah_id' => null,
                'mem_end_surah_id' => null,
                'mem_start_ayah_id' => null,
                'mem_end_ayah_id' => null,
                'mem_pages' => null,

                'rev_start_surah_id' => null,
                'rev_end_surah_id' => null,
                'rev_start_ayah_id' => null,
                'rev_end_ayah_id' => null,
                'rev_pages' => null,
            ];
        }
        $data['recitationSession']['tajweed_score'] =$data['tajweed_score'] ?? 0;
        $data['recitationSession']['fluency_score'] = $data['fluency_score'] ?? 0;
        $data['recitationSession']['memory_score'] = $data['memory_score'] ?? 0;
        $data['recitationSession']["evaluation_notes"] =  $data['evaluation_notes'] ?? '';
        $data['recitationSession']["notes"] =  $data['notes'] ?? '';

        $session->update($data['recitationSession']);
        // dd($session);
        
        return parent::mutateFormDataBeforeSave($data);

    }
}
