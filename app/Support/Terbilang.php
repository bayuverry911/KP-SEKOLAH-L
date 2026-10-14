<?php

namespace App\Support;

class Terbilang
{
    /**
     * Mengubah angka menjadi kalimat rupiah, contoh: 150000 => "Seratus lima puluh ribu rupiah".
     */
    public static function rupiah(int|float|string $angka): string
    {
        $n = (int) round((float) $angka);

        $kata = $n === 0 ? 'nol' : trim(self::angka($n));

        return ucfirst($kata) . ' rupiah';
    }

    private static function angka(int $n): string
    {
        $dasar = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($n < 12) {
            return $dasar[$n];
        }
        if ($n < 20) {
            return self::angka($n - 10) . ' belas';
        }
        if ($n < 100) {
            return self::angka(intdiv($n, 10)) . ' puluh' . ($n % 10 ? ' ' . self::angka($n % 10) : '');
        }
        if ($n < 200) {
            return 'seratus' . ($n - 100 ? ' ' . self::angka($n - 100) : '');
        }
        if ($n < 1000) {
            return self::angka(intdiv($n, 100)) . ' ratus' . ($n % 100 ? ' ' . self::angka($n % 100) : '');
        }
        if ($n < 2000) {
            return 'seribu' . ($n - 1000 ? ' ' . self::angka($n - 1000) : '');
        }
        if ($n < 1000000) {
            return self::angka(intdiv($n, 1000)) . ' ribu' . ($n % 1000 ? ' ' . self::angka($n % 1000) : '');
        }
        if ($n < 1000000000) {
            return self::angka(intdiv($n, 1000000)) . ' juta' . ($n % 1000000 ? ' ' . self::angka($n % 1000000) : '');
        }

        return self::angka(intdiv($n, 1000000000)) . ' miliar' . ($n % 1000000000 ? ' ' . self::angka($n % 1000000000) : '');
    }
}
