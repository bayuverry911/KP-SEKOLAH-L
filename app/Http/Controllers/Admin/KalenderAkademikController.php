<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\PeriodePembelajaran;
use Illuminate\Http\Request;

/**
 * Kalender akademik: tanggal merah (hari libur) & periode pembelajaran.
 * Dipakai oleh Rekap Absen Siswa (penanda libur + filter rentang pembelajaran).
 */
class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->query('tahun', date('Y'));

        $liburs = HariLibur::whereYear('tanggal', $tahun)->orderBy('tanggal')->get();
        $periodes = PeriodePembelajaran::orderBy('tanggal_mulai')->get();

        return view('admin.kalender.index', compact('liburs', 'periodes', 'tahun'));
    }

    public function storeLibur(Request $request)
    {
        $data = $request->validate([
            'tanggal'    => 'required|date|unique:hari_liburs,tanggal',
            'keterangan' => 'required|string|max:100',
            'jenis'      => 'required|in:nasional,sekolah',
        ], [
            'tanggal.unique' => 'Tanggal tersebut sudah terdaftar sebagai hari libur.',
        ]);

        HariLibur::create($data);

        return redirect()->route('admin.kalender.index', ['tahun' => substr($data['tanggal'], 0, 4)])
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Isi cepat libur nasional yang tanggalnya TETAP setiap tahun.
     * Libur yang berubah tiap tahun (Idul Fitri, Idul Adha, Nyepi, Waisak, dll.)
     * harus diinput manual sesuai SKB 3 Menteri tahun berjalan.
     */
    public function isiLiburTetap(Request $request)
    {
        $request->validate(['tahun' => 'required|integer|between:2020,2100']);
        $tahun = (int) $request->tahun;

        $tetap = [
            '01-01' => 'Tahun Baru Masehi',
            '05-01' => 'Hari Buruh Internasional',
            '06-01' => 'Hari Lahir Pancasila',
            '08-17' => 'Hari Kemerdekaan RI',
            '12-25' => 'Hari Raya Natal',
        ];

        $baru = 0;
        foreach ($tetap as $md => $nama) {
            $tgl = "{$tahun}-{$md}";
            if (! HariLibur::whereDate('tanggal', $tgl)->exists()) {
                HariLibur::create(['tanggal' => $tgl, 'keterangan' => $nama, 'jenis' => 'nasional']);
                $baru++;
            }
        }

        return redirect()->route('admin.kalender.index', ['tahun' => $tahun])
            ->with('success', "{$baru} libur tanggal tetap ditambahkan untuk {$tahun}. Libur yang tanggalnya berubah tiap tahun (mis. Idul Fitri) tetap perlu diinput manual.");
    }

    public function destroyLibur(HariLibur $libur)
    {
        $tahun = $libur->tanggal->year;
        $libur->delete();

        return redirect()->route('admin.kalender.index', ['tahun' => $tahun])
            ->with('success', 'Hari libur dihapus.');
    }

    public function storePeriode(Request $request)
    {
        $data = $request->validate([
            'nama'            => 'required|string|max:100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        PeriodePembelajaran::create($data);

        return redirect()->back()->with('success', 'Periode pembelajaran berhasil ditambahkan.');
    }

    public function destroyPeriode(PeriodePembelajaran $periode)
    {
        $periode->delete();

        return redirect()->back()->with('success', 'Periode pembelajaran dihapus.');
    }
}
