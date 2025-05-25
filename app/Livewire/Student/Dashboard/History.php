<?php

namespace App\Livewire\Student\Dashboard;

use App\Models\RecitationSession;
use Livewire\Component;

class History extends Component
{
    public $history;

    public function mount()
    {
        $this->history = RecitationSession::where('student_id', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.student.dashboard.history');
    }
}

