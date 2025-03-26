<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Candidate;
use App\Models\Evaluation;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\RecitationSession;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Glob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\StudentAccountCreated;
use App\Notifications\TeacherAccountCreated;
use App\Notifications\CandidateEvaluationNotification;



/**  define settings  */
if (! function_exists('settings')) {
    function settings(string $key, mixed $default = null)
    {
        return app(GeneralSettings::class)->{$key} ?? $default;
    }
}
/**end settings */


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
        try {
            DB::beginTransaction();

            if (Evaluation::where('candidate_id', $candidate->id)->exists()) {
                Notification::make()
                    ->title('المترشح لديه تقييم بالفعل!')
                    ->warning()
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

            Notification::make()
                ->title('تم إرسال المترشح إلى المقابلة بنجاح!')
                ->success()
                ->send();
        } catch (\Exception $ex) {

            Db::rollBack();

            Notification::make()
                ->title(' محاولة لانشاء نفس الطالب لنفس المترشح!')
                ->success()
                ->send();
        }
        // إشعار بنجاح الإرسال

    }
}

/*** accept candidate and create a new user**/

if (!function_exists('acceptedStudent')) {
    function acceptedStudent($candidate)
    {
        try {
            DB::beginTransaction();
            $password = Str::random(8);

            $user = User::create([
                'name' => $candidate->full_name,
                'email' => $candidate->email,
                'password' => Hash::make($password),
                'type' => 'student',
                'phone' => $candidate->phone
            ]);

            // إنشاء سجل طالب
            Student::create([
                'user_id' => $user->id,
                'teacher_id' => $candidate->teacher_id,
                'candidate_id' => $candidate->id,
                'start_date' => now(),
            ]);
            $user->assignRole('Student');
            $candidate->update(['status' => 'accepted'], ['evaluated' => true]);
            DB::commit();
            $user->notify(new StudentAccountCreated($user->email,  $password));
        } catch (\Exception $ex) {
            Db::rollBack();

            Notification::make()
                ->title('   الطالب تم انشأه مسبقا يرجى مراجعة قائمة الطلاب !')
                ->danger()
                ->send();
        }
    }
};


// if (!function_exists('evaluateCandidate')) {
//     function evaluateCandidate($candidate)
//     {
//         try {
//             DB::beginTransaction();

//             $evaluation = Evaluation::where('candidate_id', $candidate->id)->first();
//             if (!$evaluation) {
//                 throw new Exception('Evaluation record not found');
//             }

//             if ($evaluation->total_score >= 80) {
//                 $evaluation->update(['status' => 'passed']);
                
//                 $password = Str::random(8);
//                 $user = User::where('email', $candidate->email)
//                     ->orWhere('phone', $candidate->phone)
//                     ->first();

//                 if (!$user) {
//                     $user = User::create([
//                         'name' => $candidate->full_name,
//                         'email' => $candidate->email,
//                         'password' => Hash::make($password),
//                         'type' => 'student',
//                         'phone' => $candidate->phone
//                     ]);

//                     // تعيين الصلاحيات للطالب
//                     $user->assignRole('student');

//                     // إشعار المترشح
//                     Notification::send($candidate, new CandidateEvaluationNotification('accepted', $user->email, $password));
//                 }

//                 // إنشاء سجل الطالب إذا لم يكن موجودًا
//                 $student = Student::where('user_id', $user->id)->first();
//                 if (!$student) {
//                     Student::create([
//                         'user_id' => $user->id,
//                         'teacher_id' => $candidate->teacher_id,
//                         'candidate_id' => $candidate->id,
//                         'start_date' => now(),
//                     ]);
//                 }

//                 $candidate->update(['status' => 'accepted']);
                
//                 Notification::make()
//                     ->title('تهانينا! لقد تم قبول الطالب.')
//                     ->success()
//                     ->send();
//             } else {
//                 $candidate->update(['status' => 'pending']);

//                 Notification::make()
//                     ->title('لم يتم القبول! حاول مرة أخرى لاحقًا.')
//                     ->warning()
//                     ->send();
//             }

//             DB::commit();
//         } catch (\Exception $ex) {
//             DB::rollBack();

//             Notification::make()
//                 ->title('حدث خطأ ما! الرجاء المحاولة لاحقًا.')
//                 ->danger()
//                 ->send();
//         }
//     }
// }




















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
