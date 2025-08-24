<?php

use App\Http\Controllers\AdmissionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');


Route::get('/', fn() => Inertia::render('Home'))->name('home');

//Route::get('/dashboard', function () {
//    $user = auth()->user();
//
//    if (! $user) {
//        return redirect()->route('login');
//    }
//
//    if ($user->role === 'admin') {
//        return Inertia::render('admin/Dashboard', ['user' => $user]);
//    }
//
//    if ($user->role === 'guardian') {
//        return Inertia::render('guardian/Dashboardr', ['user' => $user]);
//    }
//
//    return Inertia::render('Dashboard/Unknown', ['user' => $user]);
//})->middleware(['auth', 'verified'])->name('dashboard');


Route::prefix('admissions')->name('admissions.')->group(function () {
    Route::get('/', fn() => Inertia::render('Admission'))->name('index');
    Route::post('/', [AdmissionController::class, 'store'])->name('store');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
