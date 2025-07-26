<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Mail\NewStudentNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class CandidateController extends Controller
{


    public function create()
    {

        $recitations = settings("reading_types",[]);

        $qualifications = settings("qualifications",[]);
        $quranLevels = Candidate::getQuranLevels();

        $ijazas = settings("ijaza_types",[]);


        return view('Candidate.create', compact( 'recitations', 'qualifications', 'quranLevels', 'ijazas'));
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
            'national_id'        => 'required|string|max:255',
            'phone'              => 'required|unique:candidates|unique:users|string|max:20',
            'email'              => 'required|email|unique:candidates,email|unique:users,email',
            'password'           => ['required', 'confirmed',  Password::defaults()],
            'birthdate'          => ['required', 'date','before_or_equal:'.Carbon::now()->subYears(settings('min_age',10))->toDateString(),
                                    'after_or_equal:'.Carbon::now()->subYears(settings('max_age',70))->toDateString()],
            'qualification'      => 'required|string|max:255',

            'program_type'       => 'required|in:' . implode(',', array_keys(Candidate::getProgramTypes())),

            'quran_level'        => 'required|in:' . implode(',', array_keys(Candidate::getQuranLevels())),
            'has_ijaza'          => 'nullable|boolean',


            'ijaza_types'  => 'required_if:has_ijaza,1|array',
            'ijaza_types.*'      => 'in:' . implode(',', array_keys(settings("ijaza_types"))),

            'desired_recitation'     => 'required_if:program_type,maqraa|string',

            'self_evaluation'    => 'required|integer',

            'qualification_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'audio_recitation'   => 'required|file|mimes:mp3,wav,ogg|max:5120',
        ]);

        if ($request->hasFile('qualification_file')) {
            $validated['qualification_file'] = $request->file('qualification_file')->store('qualification_files', 'public');
        }
        if ($request->hasFile('audio_recitation')) {
            $validated['audio_recitation'] = $request->file('audio_recitation')->store('audio_recitations', 'public');
        }


        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => 'student',
            'phone' => $validated['phone'],
            'acount_status' => false,
        ]);


        // don't assing role to decline access to control panel
        // $user->assignRole('Student');


        $validated['user_id'] = $user->id;
        $validated['has_ijaza'] = $validated['has_ijaza'] ?? false;
        Candidate::create($validated);

        // Send email to admin
        // Mail::to(env('MAIL_FROM_ADDRESS')) // Replace with your admin email
        // ->send(new NewStudentNotification($user));
        
        return back()->with('success', 'تم تقديم الطلب بنجاح.');
    }
}
