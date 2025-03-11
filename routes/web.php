<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\RecitationSessionControler;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');


Route::get('/candidate', [CandidateController::class, 'create'])->name('candidate.create');

// POST route to process the submitted form
Route::post('/candidate', [CandidateController::class, 'store'])->name('candidate.store');

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
 

Route::get('/teachers/pdf-preview', [PDFController::class, 'preview'])->name('teachers.pdf-preview');
Route::get('/teachers/pdf-download', [PDFController::class, 'download'])->name('teachers.pdf-download');
Route::get('/teachers/pdf-download', [PDFController::class, 'download'])->name('teachers.pdf-download');
//***** هذه خاصة ب تقرير الطلاب */

Route::get('/recitations/pdf-preview', [RecitationSessionControler::class, 'preview'])->name('recitations.pdf-preview');
Route::get('/recitations/pdf-download', [RecitationSessionControler::class, 'download'])->name('recitations.pdf-download');


require __DIR__.'/auth.php';
