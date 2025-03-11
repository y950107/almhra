<?php

namespace App\Http\Controllers;

use App\Jobs;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Jobs\ConvertToInterviw;
use Illuminate\Support\Facades\Bus;

class CandidateController extends Controller
{
    public function create()
    {
        return view('candidate.create');
    }

    /* public function toInterview($candidateId)
{
    $candidate = Candidate::findOrFail($candidateId);

    
    Bus::dispatch(new ConvertToInterviw($candidate));

    return redirect()->back()->with('success', 'تم إرسال المترشح إلى المقابلة بنجاح.');
} */

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'full_name'          => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'email'              => 'required|email|unique:candidates,email',
            'birthdate'          => 'required|date',
            'qualification'      => 'required|string|max:255',
            'quran_level'        => 'required|string|max:255',
            'has_ijaza'          => 'required|in:0,1',
            'ijaza_types'        => 'nullable|string|max:255',
            'desired_recitations'=> 'nullable|string|max:255',
            'self_evaluation'    => 'nullable|string',
            'teacher_id'         => 'required|integer',
            'qualification_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
            'audio_recitation'   => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        
        if ($request->hasFile('qualification_file')) {
            $validated['qualification_file'] = $request->file('qualification_file')->store('qualification_files', 'public');
        }
        if ($request->hasFile('audio_recitation')) {
            $validated['audio_recitation'] = $request->file('audio_recitation')->store('audio_recitations', 'public');
        }

        
        Candidate::create($validated);

        return redirect()->route('candidate.create')->with('success', 'تم تقديم الطلب بنجاح.');
    }


}
