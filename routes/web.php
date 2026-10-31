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
use App\Http\Controllers\Admin\KalenderAkademikController;
use App\Http\Controllers\AbsenSiswaController;
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

    // Guru pengganti (di-plot admin per tanggal)
    Route::post('/jadwal/{jadwal}/pengganti', [JadwalController::class, 'storePengganti'])->name('jadwal.pengganti.store');
    Route::delete('/jadwal-pengganti/{pengganti}', [JadwalController::class, 'destroyPengganti'])->name('jadwal.pengganti.destroy');

    // Kalender akademik: hari libur nasional/sekolah + periode pembelajaran
    Route::get('/kalender', [KalenderAkademikController::class, 'index'])->name('kalender.index');
    Route::post('/kalender/libur', [KalenderAkademikController::class, 'storeLibur'])->name('kalender.libur.store');
    Route::post('/kalender/libur-tetap', [KalenderAkademikController::class, 'isiLiburTetap'])->name('kalender.libur.tetap');
    Route::delete('/kalender/libur/{libur}', [KalenderAkademikController::class, 'destroyLibur'])->name('kalender.libur.destroy');
    Route::post('/kalender/periode', [KalenderAkademikController::class, 'storePeriode'])->name('kalender.periode.store');
    Route::delete('/kalender/periode/{periode}', [KalenderAkademikController::class, 'destroyPeriode'])->name('kalender.periode.destroy');

    Route::get('/absen', [AbsensiGuruController::class, 'rekapAdminIndex'])->name('absen');
    Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
    Route::post('/spp', [SppController::class, 'store'])->name('spp.store');
    Route::get('/spp-audit', [SppController::class, 'audit'])->name('spp.audit');
    Route::get('/spp/{spp}/kuitansi', [SppController::class, 'kuitansi'])->name('spp.kuitansi');
    Route::put('/spp/{spp}', [SppController::class, 'update'])->name('spp.update');
    Route::delete('/spp/{spp}', [SppController::class, 'destroy'])->name('spp.destroy');
});

// ROUTE KHUSUS GURU
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // 1. Dashboard Guru
    Route::get('/dashboard', [AbsensiGuruController::class, 'dashboard'])->name('dashboard');

    // 2. Absensi Guru Mandiri (Datang/Pulang)
    Route::get('/absen', [AbsensiGuruController::class, 'presensiGuruView'])->name('absen');
    Route::post('/absen', [AbsensiGuruController::class, 'store'])->name('absen.store');

    // 3. Absensi Siswa - AKSES SATU PINTU
    //    Hanya dari kartu jadwal hari ini di dashboard. Tidak ada menu/halaman absen manual.
    Route::get('/absen-siswa/{jadwal}', [AbsenSiswaController::class, 'form'])->name('absen-siswa.form');
    Route::post('/absen-siswa/{jadwal}', [AbsenSiswaController::class, 'simpan'])->name('absen-siswa.simpan');

    // Absen susulan: tanggal terlewat yang belum diabsen (halaman absen yang sama, bukan pop-up)
    Route::get('/absen-siswa/{jadwal}/susulan/{tanggal}', [AbsenSiswaController::class, 'formSusulan'])
        ->where('tanggal', '\d{4}-\d{2}-\d{2}')->name('absen-siswa.susulan');
    Route::post('/absen-siswa/{jadwal}/susulan/{tanggal}', [AbsenSiswaController::class, 'simpanSusulan'])
        ->where('tanggal', '\d{4}-\d{2}-\d{2}')->name('absen-siswa.susulan.simpan');

    // 4. Rekap Absen Siswa
    Route::get('/rekap-siswa', [AbsenSiswaController::class, 'rekap'])->name('rekap-siswa');

    // 5. Hapus absen SATU HARI untuk seluruh siswa (tidak ada hapus per siswa)
    Route::delete('/rekap-siswa/hari', [AbsenSiswaController::class, 'hapusHari'])->name('rekap-siswa.hapus-hari');
});

require __DIR__.'/auth.php';