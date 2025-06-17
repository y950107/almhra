<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use App\Filament\Resources\CandidateResource;
use App\Models\Candidate;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateCandidate extends CreateRecord
{
    protected static string $resource = CandidateResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 'student',
            'phone' => $data['phone'],
            'acount_status' => false,
        ]);

        $data['user_id'] = $user->id;

        return static::getModel()::create($data);
    }
}
