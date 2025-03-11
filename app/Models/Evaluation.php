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
        return $this->belongsTo(Candidate::class);
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

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($evaluation) {
            if ($evaluation->total_score >= 80) {
                //$evaluation->update(['status' => 'passed']);
                DB::table('evaluations')
                    ->where('id', $evaluation->id)
                    ->update(['status' => 'passed']);

                $password = Str::random(8);
                $user = User::where('email', $evaluation->candidate->full_name . '@gmail.com')
                    ->orWhere('phone', $evaluation->candidate->phone)
                    ->first();

                if (!$user) {
                    // ✅ إنشاء حساب مستخدم جديد للطالب
                    $password = Str::random(8);
                    $user = User::create([
                        'name' => $evaluation->candidate->full_name,
                        'email' => $evaluation->candidate->full_name . '@gmail.com',
                        'password' => Hash::make($password),
                        'type' => 'student',
                        'phone' => $evaluation->candidate->phone
                    ]);
                    $evaluation->candidate->notify(new CandidateEvaluationNotification('accepted', $user->email, $password));
                }
                $student = Student::where('user_id', $user->id)->first();
                if (!$student) {
                    // ✅ إنشاء سجل جديد للطالب
                    Student::create([
                        'user_id' => $user->id,
                        'teacher_id' => $evaluation->candidate->teacher_id,
                        'candidate_id' => $evaluation->candidate->id,
                        'start_date' => now(),
                    ]);
                }

                $evaluation->candidate->update(['status' => 'accepted']);
            } else {

                $evaluation->candidate->update(['status' => 'pending']);
            }
        });
    }

    public function convertToStudent()
{
    if ($this->status !== 'passed') {
        return;
    }

    // إنشاء طالب جديد من بيانات المترشح
    Student::create([
        'name' => $this->candidate->full_name,
        'email' => $this->candidate->email,
        'phone' => $this->candidate->phone,
        'candidate_id' => $this->candidate_id,
    ]);

    // حذف التقييم بعد التحويل
    $this->delete();
}







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
