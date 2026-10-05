<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\SppController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\DashboardController; // <--- DITAMBAHKAN UNTUK DASHBOARD ADMIN DINAMIS
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiGuruController;
use App\Models\User;

// HALAMAN UTAMA
Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();

        if (strtoupper($user->role) === 'ADMIN' || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return redirect()->route('admin.dashboard');
        }

        if (strtoupper($user->role) === 'GURU' || (method_exists($user, 'isGuru') && $user->isGuru())) {
            return redirect()->route('guru.dashboard');
        }
    }

    return redirect()->route('login');
});

// DASHBOARD FALLBACK
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();

    if (strtoupper($user->role) === 'ADMIN' || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('guru.dashboard');
})->name('dashboard');

// PROFILE
Route::middleware('auth')->group(function () {
    if (class_exists(ProfileController::class)) {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    }
});

// ROUTE KHUSUS ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin menggunakan DashboardController agar data real dari database
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UserController::class);
    Route::resource('data-guru', GuruController::class)->names('guru');
    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('mapel', MapelController::class);

    // RUTE JADWAL MENGAJAR GURU (ADMIN)
    if (class_exists(JadwalController::class)) {
        Route::resource('jadwal', JadwalController::class);
    }

    Route::get('/absen', [AbsensiGuruController::class, 'rekapAdminIndex'])->name('absen');
    Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
    Route::post('/spp', [SppController::class, 'store'])->name('spp.store');
});

// ROUTE KHUSUS GURU
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // 1. Dashboard Guru
    Route::get('/dashboard', [AbsensiGuruController::class, 'dashboard'])->name('dashboard');

    // 2. Absensi Guru Mandiri (Datang/Pulang)
    Route::get('/absen', [AbsensiGuruController::class, 'presensiGuruView'])->name('absen');
    Route::post('/absen', [AbsensiGuruController::class, 'store'])->name('absen.store');

    // 3. Absensi Siswa (Auto-Fill Kelas & Mapel)
    Route::get('/absen-siswa', [AbsensiGuruController::class, 'formAbsenSiswa'])->name('absen-siswa');
    Route::post('/absen/sesi', [AbsensiGuruController::class, 'simpanSesi'])->name('absen.simpanSesi');
    Route::get('/absen/siswa/dashboard', [AbsensiGuruController::class, 'dashboardAbsenSiswa'])->name('absen.siswa.dashboard');
    Route::get('/absen/siswa', [AbsensiGuruController::class, 'dashboardAbsenSiswa'])->name('absen.siswa');
    Route::post('/absen/siswa/batch', [AbsensiGuruController::class, 'simpanAbsenSiswaBatch'])->name('absen.siswa.batch');

    // 4. Rekap Absen Siswa
    Route::get('/rekap-siswa', [AbsensiGuruController::class, 'rekapSiswaIndex'])->name('rekap-siswa');

    // 5. Hapus Rekap Absen Siswa (per siswa, berdasarkan filter kelas/bulan/tahun aktif)
    Route::delete('/rekap-siswa/{siswa}', [AbsensiGuruController::class, 'hapusRekapSiswa'])->name('rekap-siswa.hapus');
});

require __DIR__.'/auth.php';