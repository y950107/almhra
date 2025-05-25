<?php

namespace App\Jobs;

use App\Models\Candidate;
use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentAccountCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConvertCandidateToStudent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Candidate $candidate) {}

    public function handle(): void
    {
        // إنشاء مستخدم جديد
        $password = Str::random(8);

        $user = User::create([
            'name' => $this->candidate->full_name,
            'email' => $this->candidate->email,
            'password' => Hash::make($password),
            'type' => 'student',
            'phone' => $this->candidate->phone
        ]);

        // إنشاء سجل طالب
        Student::create([
            'user_id' => $user->id,
            'teacher_id' => $this->candidate->teacher_id,
            'start_date' => now(),
        ]);

        $this->candidate->update(['status' => 'accepted']);
        $user->notify(new StudentAccountCreated('email', $password));



    }
}
