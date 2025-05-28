<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }



    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['candidate']['program_type'])) {
            $record->candidate->program_type = $data['candidate']['program_type'];
            $record->candidate->save();
        }

        $filteredData = collect($data)->except('candidate')->toArray();

        return  parent::handleRecordUpdate($record, $filteredData);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }



}
