<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): Response
    {
        $phone = trim((string)$request->query('phone', ''));
        $defaultPassword = false;

        if ($phone !== '') {
            // ⚠️ Catatan: Pola ini berpotensi user-enumeration. Pakai hanya jika kamu setuju.
            $user = User::where('phone', $phone)->first();

            // defaultPassword = true kalau hash password di DB == nomor telepon user
            if ($user && is_string($user->password) && Hash::check($user->phone, $user->password)) {
                $defaultPassword = true;
            }
        }

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'defaultPassword' => $defaultPassword,
            'prefill' => ['phone' => $phone], // untuk mengisi ulang input phone di FE
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
