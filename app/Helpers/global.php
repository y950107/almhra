<?php

use App\Models\RecitationSession;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Candidate;
use App\Models\Evaluation;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Symfony\Component\Finder\Glob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\TeacherAccountCreated;



//// manage teacher 
if (!function_exists('TeacherToUser')) {
    function TeacherToUser($teacher)
    {

        if (Teacher::where('id', $teacher->user_id)->whereNull('user_id')->exists()) {
            Notification::make()
                ->title('  لقد تم انشاء حساب مسبقا !!')
                ->warning()
                ->send();
            return;
        }
        $password = Str::random(8);

        $user = User::create([
            'name' =>  $teacher->name,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'password' => Hash::make($password),
            'type' => 'Teacher'
        ]);



        $user->assignRole('Teacher');
        $teacher->update(['user_id' => $user->id]);
        $user->notify(new TeacherAccountCreated($password));
    }
}

//***************end manage teacher */



//***************  start manage candidate ************/

if (!function_exists('sendToInterview')) {
    function sendToInterview($candidate)
    {

    
        if (Evaluation::where('candidate_id', $candidate->id)->exists()) {
            Notification::make()
                ->title('المترشح لديه تقييم بالفعل!')
                ->danger()
                ->send();
            return;
        }

    
        Evaluation::create([
            'candidate_id' => $candidate->id,
            'evaluator_id' => $candidate->teacher_id,
            'tajweed_score' => 0,
            'voice_score' => 0,
            'memorization_score' => 0,
            'total_score' => 0,
            'note' => null,
            'status' => 'pending',
        ]);

        // تحديث حالة المترشح
        $candidate->update(['status' => 'interview']);

        // إشعار بنجاح الإرسال
        Notification::make()
            ->title('تم إرسال المترشح إلى المقابلة بنجاح!')
            ->success()
            ->send();
    }
}



//***************  end manage candidate ************/
// if (!function_exists('PageCount')) {
//     function PageCount(RecitationSession $recitation, $startPage, $endPage)
//     {

        
//         $Lastrecitation = RecitationSession::where('student_id', $recitation->student_id)
//             ->where('session_date', '<', $recitation->session_date)
//             ->orderBy('session_date', 'desc')
//             ->first();

//         if (!$startPage || !$endPage) {
//             return 0;
//         }

        
//         if ($Lastrecitation === null) {
//             return ($endPage - $startPage);
//         }

    
//         if ($Lastrecitation->actual_end_page === $startPage) {
//             return  abs(($end_page ?? $startPage) - $startPage);
//         }


//     }
// }
////*****************page count verses data page */






//***historique */


// if (!function_exists('ToInterview')) {
//     function ToInterview($candidate)
//     {
//         //dd($candidate);
//         Evaluation::create([
//             'candidate_id'       => $candidate->id,
//             'evaluator_id'       => $candidate->teacher_id,
//             'tajweed_score'      => 1,
//             'voice_score'        => 1,
//             'memorization_score' => 1,
//             'status'             => 'pending',
//         ]);
//         $candidate->update(['status' => 'interview']);
//     }
// };


/* if (!function_exists('acceptedStudent')) {
    function acceptedStudent($candidate)
    {
        $password = Str::random(8);

        $user = User::create([
            'name' => $candidate->full_name,
            'email' => $candidate->name . '@gmail.com',
            'password' => Hash::make($password),
            'type' => 'student',
            'phone' => $candidate->phone
        ]);

        // إنشاء سجل طالب
        Student::create([
            'user_id' => $user->id,
            'teacher_id' => $candidate->teacher_id,
            'start_date' => now(),
        ]);
        $user->assignRole('Student');
        $candidate->update(['status' => 'accepted']);
        $user->notify(new StudentAccountCreated('password'));
    }
}; */

// انشاء معلم كمستخدم 

// if (! function_exists('Tointerview')) {
//     function Tointerview($candidateId)
//     {
//         // البحث عن المترشح والتحقق من وجوده
//         $candidate = Candidate::find($candidateId);

//         if (!$candidate) {
//             session()->flash('error', 'المترشح غير موجود!');
//             return;
//         }

        
//         if (Evaluation::where('candidate_id', $candidate->candidate_id)->exists()) {
//             session()->flash('error', 'المترشح لديه تقييم بالفعل!');
//             return;
//         }

        
//         Evaluation::create([
//             'candidate_id' => $candidate->id,
//             'evaluator_id' => $candidate->teacher_id, // التأكد من أن teacher_id موجود
//             'tajweed_score' => 0,
//             'voice_score' => 0,
//             'memorization_score' => 0,
//             'total_score' => 0,
//             'note' => null,
//             'status' => 'pending',
//         ]);

//         // تحديث حالة المترشح
//         $candidate->update(['status' => 'interview']);

//         // إظهار رسالة نجاح
//         session()->flash('success', 'تم إرسال المترشح إلى المقابلة بنجاح.');
//     }
