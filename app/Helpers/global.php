<?php

use App\Models\Evaluation;
use App\Models\Student;
use App\Models\User;
use App\Notifications\CandidateAccepted;
use App\Notifications\CandidateEvaluationNotification;
use App\Notifications\EvaluationNotif;
use App\Notifications\StudentAccountCreated;
use App\Notifications\TeacherAccountCreated;
use App\Services\Moshaf_madina_Service;
use App\Settings\GeneralSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**  define settings  */
if (! function_exists('settings')) {
    function settings(string $key, mixed $default = null)
    {
        return app(GeneralSettings::class)->{$key} ?? $default;
    }
}
/**end settings */

if (! function_exists('getSurahName')) {
    function getSurahName($surahId)
    {

        $quranService = app(Moshaf_madina_Service::class);
        $surahs = $quranService->getSurahs();
        return collect($surahs)->where('id', $surahId)->first()['name'] ?? null;
    }
}

//// manage teacher
if (!function_exists('TeacherToUser')) {
    function TeacherToUser($teacher,$data)
    {
        try {
            // Check if a user account was already created for this teacher
            if ($teacher->user_id !== null) {
                Notification::make()
                    ->title('لقد تم انشاء حساب مسبقا !!')
                    ->warning()
                    ->send();
                return;
            }


            $password = $data['password'] ;

            // Create user
            $user = User::create([
                'name' => $teacher->name,
                'email' => $teacher->email,
                'phone' => $teacher->phone,
                'password' => Hash::make($password),
                'type' => 'teacher',
            ]);

            // Assign role and link user to teacher
            $user->assignRole('Teacher');
            $user->save();
            $teacher->update(['user_id' => $user->id]);

            // Notify user
            $user->notify(new TeacherAccountCreated($password));

            Notification::make()
                ->title('تم إنشاء الحساب بنجاح')
                ->success()
                ->send();
        } catch (\Illuminate\Database\QueryException $e) {
            Notification::make()
                ->title('خطأ في قاعدة البيانات')
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('حدث خطأ غير متوقع')
                ->danger()
                ->send();
        }
    }
}

//***************end manage teacher */



//***************  start manage candidate ************/

if (!function_exists('sendToInterview')) {
    function sendToInterview($candidate)
    {
        try {

            if (Evaluation::where('candidate_id', $candidate->id)->exists()) {
                Notification::make()
                    ->title('المترشح لديه تقييم بالفعل!')
                    ->warning()
                    ->send();
                return;
            }

            if (is_null($candidate->teacher_id))  {

                Notification::make()
                    ->title('المترشح ليس لديه معلم!')
                    ->danger()
                    ->send();
                return;
            }
            DB::beginTransaction();
            Evaluation::create([
                'candidate_id' => $candidate->id,
                'evaluator_id' => $candidate?->teacher?->user->id,
                'tajweed_score' => 0,
                'voice_score' => 0,
                'memorization_score' => 0,
                'total_score' => 0,
                'note' => null,
                'status' => 'pending',
            ]);

            $candidate->update(['status' => 'interview']);

            DB::commit();

            $candidate->notify((new EvaluationNotif($candidate))->afterCommit());

            Notification::make()
                ->title('تم إرسال المترشح إلى المقابلة بنجاح!')
                ->success()
                ->send();
        } catch (\Exception $ex) {
            DB::rollBack();

            Notification::make()
                ->title('حدث خطأ أثناء إرسال المترشح للمقابلة!')
                ->body($ex->getMessage())
                ->danger()
                ->send();
        }
    }

}

/*** accept candidate and create a new user**/

if (!function_exists('acceptedStudent')) {
    function acceptedStudent($candidate)
    {
        $password = "password";

        try {

            if (is_null($candidate->teacher_id))  {

                Notification::make()
                    ->title('المترشح ليس لديه معلم!')
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();



            if (!$candidate->user_id) {
                $user = User::create([
                    'name' => $candidate->full_name,
                    'email' => $candidate->email,
                    'password' => Hash::make($password),
                    'type' => 'student',
                    'phone' => $candidate->phone,
                    'acount_status' => true
                ]);
                $user->assignRole('Student');
            }
            else {
                $user = User::findOrFail($candidate->user_id);
                $user->acount_status = true;
            }

            $user->save();

            // إنشاء سجل طالب
            Student::create([
                'user_id' => $user->id,
                'teacher_id' => $candidate->teacher_id,
                'candidate_id' => $candidate->id,
                'start_date' => now(),
            ]);

            $candidate->update(['status' => 'accepted'], ['evaluated' => true]);

            DB::commit();

            $user->notify((new StudentAccountCreated($user->email, $password))->afterCommit());

            Notification::make()
                ->title('  تم إرسال المترشح إلى المقابلة بنجاح! مع اشعار ')
                ->success()
                ->send();
        } catch (\Exception $ex) {
            Db::rollBack();

            Notification::make()
                ->title('   الطالب تم انشأه مسبقا يرجى مراجعة قائمة الطلاب !')
                ->danger()
                ->send();
        }
    }
};

/**************************** */

if (!function_exists('acceptedCandidate')) {
    function acceptedCandidate($candidate)
    {
        $password = 'password';

        try {

            if (is_null($candidate->teacher_id))  {

                Notification::make()
                    ->title('المترشح ليس لديه معلم!')
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();



            if (!$candidate->user_id) {
                $user = User::create([
                    'name' => $candidate->full_name,
                    'email' => $candidate->email,
                    'password' => Hash::make($password),
                    'type' => 'student',
                    'phone' => $candidate->phone,
                    'acount_status' => true
                ]);
                $user->assignRole('Student');
            }
            else {
                $user = User::findOrFail($candidate->user_id);
                $user->acount_status = true;
            }

            $user->save();
            // إنشاء سجل طالب
            Student::create([
                'user_id' => $user->id,
                'teacher_id' => $candidate->teacher_id,
                'candidate_id' => $candidate->id,
                'start_date' => now(),
            ]);

            $candidate->update(['status' => 'accepted'], ['evaluated' => true]);

            DB::commit();

            $user->notify((new CandidateAccepted($password))->afterCommit());

            Notification::make()
                ->title(' تم ارسال اشعار للطالب!')
                ->info()
                ->send();

        } catch (\Exception $ex) {
            Db::rollBack();

            Notification::make()
                ->title('   الطالب تم انشأه مسبقا يرجى مراجعة قائمة الطلاب !')
                ->danger()
                ->send();
        }
    }
};


/************************ */


if (!function_exists('evaluateCandidate')) {
    function evaluateCandidate($evaluations)
    {

        try {

            $evaluation = Evaluation::find($evaluations->id);

            if (!$evaluation) {
                Notification::make()
                    ->title('التقييم غير موجود')
                    ->danger()
                    ->send();
                return;
            }




            $password = "password";

            $program = $evaluation->candidate->program_type;

            switch ($program) {
                case 'maqraa':
                    $passing_field = 'maqraa_target_percentage';
                    break;
                case 'mahir':
                    $passing_field = 'mahir_target_percentage';
                    break;
                case 'mutqin':
                    $passing_field = 'mutqin_target_percentage';
                    break;
                default:
                    $passing_field = 'passing_percentage';
                    break;
            }

            $passing_percentage = settings($passing_field, 80);


            if ($evaluation->total_score >= $passing_percentage) {
                DB::beginTransaction();

                if (!$evaluation->candidate->user_id) {
                    $user = User::create([
                        'name' => $evaluation->candidate->full_name,
                        'email' => $evaluation->candidate->email,
                        'password' => Hash::make($password),
                        'type' => 'student',
                        'phone' => $evaluation->candidate->phone,
                        'acount_status' => true
                    ]);
                    $user->assignRole('Student');
                }
                else {
                    $user = User::findOrFail($evaluation->candidate->user_id);
                    $user->acount_status = true;

                }
                $user->save();

                Student::create([
                    'user_id' => $user->id,
                    'teacher_id' => $evaluation->candidate->teacher_id,
                    'candidate_id' => $evaluation->candidate->id,
                    'start_date' => now(),
                ]);



                $evaluation->update(['status' => 'passed']);
                $evaluation->candidate->update(['status' => 'accepted', 'evaluated' => true]);

                DB::commit();


                $user->notify(new CandidateEvaluationNotification(
                    $evaluation->candidate->status->value,
                    $user->email,
                    $password
                ));

                Notification::make()
                    ->title('تهانينا! لقد تم قبوله كالطالب.')
                    ->success()
                    ->send();
            } else {

                $evaluation->update(['status' => 'failed']);
                $evaluation->candidate->update(['status' => 'pending','evaluated' => true]);

                // إرسال إشعار إلى البريد الإلكتروني للمترشح (بدون إنشاء حساب)
                $evaluation->candidate->notify(new CandidateEvaluationNotification(
                    'pending',
                    $evaluation->candidate->email,
                    null // لا يوجد كلمة مرور في حالة الرفض
                ));

                Notification::make()
                    ->title('لم يتم القبول. سيتم إدراجه ضمن قائمة الاحتياط!')
                    ->warning()
                    ->send();



            }
        } catch (\Exception $ex) {
            DB::rollBack();
            logger()->error('Evaluation failed: ' . $ex->getMessage());

            Notification::make()
                ->title('حدث خطأ ما! الرجاء المحاولة لاحقًا.')
                ->danger()
                ->send();
        }
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
