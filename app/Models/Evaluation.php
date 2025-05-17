<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Enums\EvaluationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Notifications\StudentAccountCreated;
use App\Notifications\CandidateEvaluationNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{

    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'candidate_id',
        'evaluator_id',
        'tajweed_score',
        'voice_score',
        'memorization_score',
        'notes',
        'status'
    ];

    protected $casts = [
        'status' => EvaluationStatus::class,
    ];

    // العلاقات
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function session()
    {
        return $this->belongsTo(halaka::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }


    // protected static function boot()
    // {
    //     parent::boot();

    //     try {
    //         static::updated(function ($evaluation) {
    //             if ($evaluation->total_score >= 80) {



    //                 DB::beginTransaction();

    //                 $password = Str::random(8);
    //                 $user = User::where('email', $evaluation->candidate->email)
    //                     ->orWhere('phone', $evaluation->candidate->phone)
    //                     ->first();

    //                 if (!$user) {
    //                     // ✅ إنشاء حساب مستخدم جديد للطالب
    //                     $password = Str::random(8);
    //                     $user = User::create([
    //                         'name' => $evaluation->candidate->full_name,
    //                         'email' => $evaluation->candidate->email,
    //                         'password' => Hash::make($password),
    //                         'type' => 'student',
    //                         'phone' => $evaluation->candidate->phone
    //                     ]);
    //                     $evaluation->candidate->notify(new CandidateEvaluationNotification('accepted', $user->email, $password));
    //                 }
    //                 $student = Student::where('user_id', $user->id)->first();
    //                 if (!$student) {
    //                     // ✅ إنشاء سجل جديد للطالب
    //                     Student::create([
    //                         'user_id' => $user->id,
    //                         'teacher_id' => $evaluation->candidate->teacher_id,
    //                         'candidate_id' => $evaluation->candidate->id,
    //                         'start_date' => now(),
    //                     ]);
    //                 }
    //                 $evaluation->update(['status' => 'passed']);
    //                 dd($evaluation->status);

    //                 DB::commit();

    //                 $evaluation->refresh();
    //                 $evaluation->candidate->update(['status' => 'accepted']);
    //             } else {

    //                 $evaluation->candidate->update(['status' => 'pending']);
    //             }
    //         });
    //     } catch (\Exception $ex) {

    //         throw $ex;
    //     }
    // }








    /* protected static function booted()
    {
        static::saving(function ($evaluation) {
            $evaluation->total_score = (
                $evaluation->tajweed_score +
                $evaluation->voice_score +
                $evaluation->memorization_score
            ) / 3;
        });
    } */
}
