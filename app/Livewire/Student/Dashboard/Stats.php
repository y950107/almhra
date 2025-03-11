<?php

namespace App\Livewire\Student\Dashboard;

use id;
use Livewire\Component;
use App\Models\RecitationSession;

class Stats extends Component
{
    public $totalSessions;
    public $totalAyatMemorized;
    public $progressRate;

    public function mount()
    {
        $this->totalSessions = RecitationSession::where('student_id', Auth()->id())->count();
        $this->totalAyatMemorized = RecitationSession::where('student_id', auth()->id())->sum('actual_pages');
        $this->progressRate = $this->calculateProgressRate();
    }

    private function calculateProgressRate()
    {
        $total = RecitationSession::where('student_id', auth()->id())->count();
        return $total ? round(($this->totalAyatMemorized / ($total * 10)) * 100, 2) : 0; // نسبة تقديرية
    }

    public function render()
    {
        return view('livewire.student.dashboard.stats');
    }
}
