<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Notifications\AdminAccountCreated;
use App\Notifications\TeacherAccountCreated;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $roleIds = $data['roles'] ?? [];
        unset($data['roles']); // Remove from user data

        $user = User::create($data);

        // Fetch roles by IDs and assign them
        $roles = Role::whereIn('id', $roleIds)->pluck('name');
        $user->assignRole($roles); // Accepts array of names

        // Notify based on assigned roles
        if ($user->hasRole('super_admin')) {
            $user->notify(new AdminAccountCreated($data['password']));
        } elseif ($user->hasRole('Teacher')) {
            $user->notify(new TeacherAccountCreated($data['password']));
        }

        return $user;
    }

}
