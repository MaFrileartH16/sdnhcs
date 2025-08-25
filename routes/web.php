<?php

use App\Http\Controllers\Admin\AdminApplicantController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTestController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\Guardian\GuardianApplicantController;
use App\Http\Controllers\Guardian\GuardianDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/* ========= PUBLIC ========= */
Route::get('/', fn() => Inertia::render('Home'))->name('home');

Route::get('/dashboard', function () {
    $u = auth()->user();
    abort_unless($u, 403);
    return $u->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('guardian.dashboard');
})->middleware('auth')->name('dashboard');

/* ========= ADMIN ========= */
Route::middleware(['auth', 'verified'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/applicants', [AdminApplicantController::class, 'index'])
            ->name('applicants.index');
        Route::delete('/applicants/{id}', [AdminApplicantController::class, 'destroy'])
            ->name('applicants.destroy');

        Route::get('/tests', [AdminTestController::class, 'index'])->name('tests.index');

        // settings KKM
        Route::post('/tests/settings', [AdminTestController::class, 'updateSettings'])
            ->name('tests.settings.update');

        // subjects
        Route::post('/tests/subjects', [AdminTestController::class, 'storeSubject'])
            ->name('tests.subjects.store');
        Route::put('/tests/subjects/{id}', [AdminTestController::class, 'updateSubject'])
            ->name('tests.subjects.update');
        Route::delete('/tests/subjects/{id}', [AdminTestController::class, 'destroySubject'])
            ->name('tests.subjects.destroy');
    });

/* ========= GUARDIAN ========= */
Route::middleware(['auth', 'verified'])
    ->prefix('guardian')->name('guardian.')
    ->group(function () {
        Route::get('/dashboard', [GuardianDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/applicants', [GuardianApplicantController::class, 'index'])
            ->name('applicants.index');

        // ✅ simpan/ubah nilai tes untuk seorang siswa (guardian)
        Route::post('/applicants/{student}/scores', [GuardianApplicantController::class, 'upsertScores'])
            ->name('applicants.scores.store');
    });

/* ========= ADMISSIONS (umum/guardian) ========= */
Route::prefix('admissions')->name('admissions.')->group(function () {
    Route::get('/', fn() => Inertia::render('Admission'))->name('index');
    Route::post('/', [AdmissionController::class, 'store'])->name('store');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
