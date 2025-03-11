<?php

namespace App\Livewire\Student\Dashboard;

use App\Models\halaka;
use Livewire\Component;
use App\Models\RecitationSession;

class ActiveSessions extends Component
{
    public $sessions;

    public function mount()
    {
        $this->sessions = Halaka::whereHas('recitationSessions', function ($query) {
            $query->where('student_id', auth()->id());
        })->where('halaka_status', true)->get();
    }

    public function render()
    {
        return view('livewire.student.dashboard.active-sessions');
    }
}

