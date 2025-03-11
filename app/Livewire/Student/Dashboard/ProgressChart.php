<?php

namespace App\Livewire\Student\Dashboard;

use Livewire\Component;
use App\Models\RecitationSession;

class ProgressChart extends Component
{
    public function render()
    {
        return view('livewire.student.dashboard.progress-chart');
    }
}

