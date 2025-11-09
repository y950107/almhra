<?php


use App\Models\User;
use App\Models\Surah;
use App\Models\Verse;
use App\Services\QuranService;
use App\Services\PrayerTimeService;
use App\Mail\NewStudentNotification;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CandidateController;
use App\Livewire\Recitations\ListRecitations;
use App\Http\Controllers\RecitationSessionControler;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])
//     ->middleware('guest')
//     ->name('login');


Route::get('/candidate', [CandidateController::class, 'create'])->name('candidate.create');

// POST route to process the submitted form
Route::post('/candidate', [CandidateController::class, 'store'])->name('candidate.store');
Route::get('/candidates/pdf-download', action: [PDFController::class, 'downloadCandidatesReport'])->name('candidates.pdf-download');
/* Route::get('/test-update', function () {
    $user = Auth::user();

    if (!$user instanceof User) {
        return 'المستخدم غير مسجل الدخول';
    }

    $user->update([
        'name' => 'Super Admin ',
    ]);

    return 'تم التحديث بنجاح';
}); */

/* Route::get('/test-email', function () {
    Mail::raw('هذا بريد اختبار من Laravel عبر Mailtrap', function ($message) {
        $message->to('test@example.com') // يمكنك تغيير الإيميل
                ->subject('اختبار الإرسال عبر Mailtrap');
    });

    return 'تم إرسال البريد بنجاح!';
}); */

/* Route::get('/reports/preview', [ReportsController::class, 'preview'])->name('reports.preview');
Route::get('/reports/download-pdf', [ReportsController::class, 'downloadPdf'])->name('reports.download-pdf');
Route::get('/reports/download-excel', [ReportsController::class, 'downloadExcel'])->name('reports.download-excel'); */

Route::get('/test-prayer-times', function () {
    dd(PrayerTimeService::getPrayerTimes(24.7136, 46.6753)); // 🔥 اختبار جلب أوقات الصلاة
});
// Route::get('/test', function () {
//     $user = User::find(68);
//     Mail::to(env('MAIL_FROM_ADDRESS')) // Replace with your admin email
//     ->send(new NewStudentNotification($user));
// });

Route::get('/quran/surahs', function () {
    return response()->json(Surah::all());
});

Route::get('/quran/verses/{surah_id}', function ($surah_id) {
    return response()->json(Verse::where('surah_id', $surah_id)->get());
});

//Route::get('/teachers/pdf-preview', [PDFController::class, 'preview'])->name('teachers.pdf-preview');


Route::get('/teachers/pdf-download', [PDFController::class, 'download'])->name('teachers.pdf-download');
Route::get('/students/presence/pdf-download', [PDFController::class, 'downloadStudentsPresence'])->name('students-presence.pdf-download');
Route::get('/students/pdf-download', [PDFController::class, 'downloadStudentsReport'])->name('students.pdf-download');
Route::get('/graduated_students/pdf-download', [PDFController::class, 'downloadGraduatedStudentsReport'])->name('graduated_students.pdf-download');
//***** هذي خاصة ب تقرير الطلاب */

Route::get('/recitations/pdf-preview', [RecitationSessionControler::class, 'preview'])->name('recitations.pdf-preview');
//Route::get('/recitations/pdf-download', [RecitationSessionControler::class, 'download'])->name('recitations.pdf-download');
Route::get('/recitations/pdf-download-almaqraa-report', [RecitationSessionControler::class, 'downloadMaqraaReport'])->name('recitations.pdf-download-almaqraa-report');
Route::get('/recitations/pdf-download-almahir-report', [RecitationSessionControler::class, 'downloadMahirReport'])->name('recitations.pdf-download-almahir-report');
Route::get('/recitations/pdf-download-almahir-report/detailed', [RecitationSessionControler::class, 'downloadMahirDetailedReport'])->name('recitations.pdf-download-almahir-detailed-report');

Route::get('/recitations/pdf-download-almutqin-report', [RecitationSessionControler::class, 'downloadMutqinReport'])->name('recitations.pdf-download-almutqin-report');
Route::get('/recitations/pdf-download-almutqin-report/detailed', [RecitationSessionControler::class, 'downloadMutqinDetailedReport'])->name('recitations.pdf-download-almutqin-detailed-report');

Route::get('/evaluations/pdf-download', action: [PDFController::class, 'downloadEvaluationsReport'])->name('evaluations.pdf-download');
Route::get('/evaluations/pdf-download-short', action: [PDFController::class, 'downloadEvaluationsShortReport'])->name('evaluations.pdf-download-short');
Route::get('/attendance/pdf-download', [PDFController::class, 'downloadAttendanceReport'])->name('attendance.pdf-download');

// Route::get('language/{locale}', function (string $locale) {
//     if (! in_array($locale, ['en', 'ar'])) {
//         abort(400);
//     }

//     app()->setLocale($locale);
//     session()->put('locale', $locale);


//     //\Artisan::call("config:clear");
//     Config::set('app.locale',session()->get("locale"));
//     //\Artisan::call("config:cache");
//     return redirect()->back();
// })->name('change_locale');
require __DIR__.'/auth.php';
