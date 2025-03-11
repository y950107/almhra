<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTeacherUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Teacher $teacher) {}

    public function handle(): void
    {
        $password = Str::random(10);
        
        $user = User::create([
            'name' => $this->teacher->name,
            'email' => $this->teacher->email,
            'phone' => $this->teacher->phone,
            'password' => Hash::make($password),
            'type' => 'teacher'
        ]);

        $this->teacher->update(['user_id' => $user->id]);
        
        $user->notify(new \App\Notifications\TeacherAccountCreated($password));
    }
}

