<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Sekolah (Absensi GPS Guru)
|--------------------------------------------------------------------------
| Isi nilainya lewat file .env (SEKOLAH_LAT, SEKOLAH_LNG, dst).
*/

$koordinat = function (string $key): ?float {
    $nilai = env($key);

    return ($nilai === null || $nilai === '') ? null : (float) $nilai;
};

return [
    'nama' => env('SEKOLAH_NAMA', 'SMP Djojoredjo'),
    'alamat' => env('SEKOLAH_ALAMAT', ''),

    // Titik pusat sekolah
    'latitude'  => $koordinat('SEKOLAH_LAT'),
    'longitude' => $koordinat('SEKOLAH_LNG'),

    // Jarak maksimal guru dari titik pusat sekolah (meter)
    'radius_meter' => (int) env('SEKOLAH_RADIUS', 100),

    // Akurasi GPS terburuk yang masih diterima (meter). Makin besar angkanya = makin buruk sinyalnya.
    'akurasi_maks_meter' => (int) env('SEKOLAH_AKURASI_MAKS', 100),

    // true = absen Hadir/Pulang WAJIB berada dalam radius sekolah
    'gps_wajib' => filter_var(env('SEKOLAH_GPS_WAJIB', true), FILTER_VALIDATE_BOOLEAN),

    // Tombol "Mulai Absen" siswa aktif X menit SEBELUM jam mulai jadwal (0 = tepat jam mulai).
    'absen_siswa_toleransi_menit' => (int) env('ABSEN_SISWA_TOLERANSI_MENIT', 0),
];
