<?php

namespace App\Http\Controllers;

use App\Jobs;
use App\Models\Teacher;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Jobs\ConvertToInterviw;
use Illuminate\Support\Facades\Bus;

class CandidateController extends Controller
{

    public function create()
    {
        $teachers = Teacher::select('id', 'name')->get(); 
        $recitations = ['حفص عن عاصم', 'ورش عن نافع', 'قالون عن نافع', 'الدوري عن أبي عمرو']; 
        $qualifications = ['بكالوريوس', 'ماجستير', 'دكتوراه', 'ثانوية عامة']; 
        $quranLevels = Candidate::getQuranLevels();

        $ijazas = Candidate::getIjazaTypes();


        return view('candidate.create', compact('teachers', 'recitations', 'qualifications', 'quranLevels', 'ijazas'));
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
            'qualification' => 'required|array',
            'qualification.*' => 'string|max:255',


            'quran_level'        => 'required|in:' . implode(',', array_keys(Candidate::getQuranLevels())),


            'has_ijaza'          => 'required|boolean',


            'ijaza_types'        => 'required|array',
            'ijaza_types.*'      => 'in:' . implode(',', array_keys(Candidate::getIjazaTypes())),


            'desired_recitations'     => 'required|array',
            'desired_recitations.*'   => 'string',


            'self_evaluation'    => 'nullable|integer',


            'teacher_id'         => 'required|exists:teachers,id',


            'qualification_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'audio_recitation'   => 'nullable|file|mimes:mp3,wav,ogg|max:5120',
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
