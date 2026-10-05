<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi user.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // 1. Regenerasi ID session untuk keamanan & mencegah session fixation
        $request->session()->regenerate();

        // 2. HAPUS 'intended' URL lama agar tidak mengarahkan ke URL bekas akun lain / 403 / loop redirect
        $request->session()->forget('url.intended');

        /** @var \App\Models\User $user */
        $user = $request->user();

        // 3. Arahkan secara EKSPLISIT berdasarkan Role (Gunakan redirect()->route(), BUKAN intended)
        $role = strtoupper($user->role ?? '');

        if ($role === 'ADMIN' || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'GURU' || (method_exists($user, 'isGuru') && $user->isGuru())) {
            return redirect()->route('guru.dashboard');
        }

        // Fallback jika role tidak terdefinisi
        return redirect()->route('dashboard');
    }

    /**
     * Logout user & bersihkan seluruh data session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Logout dari guard 'web'
        Auth::guard('web')->logout();

        // 2. Kosongkan semua data dari session
        $request->session()->flush();

        // 3. Invalidate session saat ini
        $request->session()->invalidate();

        // 4. Regenerasi CSRF Token agar formulir login berikutnya segar
        $request->session()->regenerateToken();

        // 5. Redirect ke halaman login (dengan header anti-cache opsional dari middleware / response)
        return redirect()->route('login');
    }
}