<?php

namespace App\Jobs;


use App\Models\Candidate;
use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertToInterviw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Candidate $candidate;
    /**
     * Create a new job instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (Evaluation::where('candidate_id', $this->candidate->id)->exists()) {
            session()->flash('error', 'المترشح لديه تقييم بالفعل!');
            return;
        }

        // إنشاء التقييم
        Evaluation::create([
            'candidate_id' => $this->candidate->id,
            'evaluator_id' => $this->candidate->evaluator_id,
            'tajweed_score' => 0,
            'voice_score' => 0,
            'memorization_score' => 0,
            'total_score' => 0,
            'note' => null,
            'status' => 'pending',
        ]);

        // تحديث حالة المترشح
        $this->candidate->update(['status' => 'interview']);


    }
}
