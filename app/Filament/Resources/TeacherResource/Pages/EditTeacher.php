<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user = $record->user()
            ->updateOrCreate([], $userData);
        $user->assignRole('Teacher');
        $data['user_id'] = $user->id;

        return parent::handleRecordUpdate($record, $data);
    }
}
