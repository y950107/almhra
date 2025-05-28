<?php

namespace App\Filament\Resources\AlMutqinRecitationResource\Pages;

use App\Filament\Resources\AlMutqinRecitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecitation extends EditRecord
{
    protected static string $resource = AlMutqinRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return AlMutqinRecitationResource::getUrl();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

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

        return parent::mutateFormDataBeforeSave($data);

    }
}
