<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\PeriodePembelajaran;
use App\Models\TahunPelajaran;
use App\Services\AbsenSiswaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Kalender akademik: Tahun Pelajaran, hari libur (tanggal merah) dan periode pembelajaran.
 * Dipakai Rekap Absen Siswa: hari libur tidak dihitung sebagai hari efektif,
 * dan tahun pelajaran aktif membatasi/mengarahkan filter tanggal guru.
 */
class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        $tahunList = TahunPelajaran::orderByDesc('tanggal_mulai')->get();

        $tahun = $request->filled('tahun')
            ? $tahunList->firstWhere('id', (int) $request->query('tahun'))
            : null;
        $tahun ??= $tahunList->firstWhere('aktif', true) ?? $tahunList->first();

        $blok   = [];
        $liburs = collect();

        if ($tahun) {
            $liburs = HariLibur::whereBetween('tanggal', [$tahun->tanggal_mulai->toDateString(), $tahun->tanggal_selesai->toDateString()])
                ->orderBy('tanggal')->get()
                ->keyBy(fn ($l) => $l->tanggal->toDateString());

            $blok = $this->bulanBlok($tahun->tanggal_mulai, $tahun->tanggal_selesai, $liburs);
        }

        $periodes = PeriodePembelajaran::orderBy('tanggal_mulai')->get();

        return view('admin.kalender.index', compact('tahunList', 'tahun', 'blok', 'liburs', 'periodes'));
    }

    // ---------------------------------------------------------------- Tahun Pelajaran

    public function storeTahun(Request $request)
    {
        $data = $request->validate([
            'nama'            => 'required|string|max:30',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        if (Carbon::parse($data['tanggal_mulai'])->diffInDays(Carbon::parse($data['tanggal_selesai'])) > 400) {
            return redirect()->back()->withInput()->with('error', 'Rentang Tahun Pelajaran maksimal sekitar 13 bulan.');
        }

        $tahun = TahunPelajaran::create($data + ['aktif' => ! TahunPelajaran::where('aktif', true)->exists()]);

        return redirect()->route('admin.kalender.index', ['tahun' => $tahun->id])
            ->with('success', "Tahun Pelajaran {$tahun->nama} ditambahkan.");
    }

    public function aktifkanTahun(TahunPelajaran $tahun)
    {
        DB::transaction(function () use ($tahun) {
            TahunPelajaran::query()->update(['aktif' => false]);
            $tahun->update(['aktif' => true]);
        });

        return redirect()->route('admin.kalender.index', ['tahun' => $tahun->id])
            ->with('success', "Tahun Pelajaran {$tahun->nama} dijadikan aktif (default filter akun guru).");
    }

    public function destroyTahun(TahunPelajaran $tahun)
    {
        $tahun->delete();

        return redirect()->route('admin.kalender.index')->with('success', 'Tahun Pelajaran dihapus. Data hari libur tetap tersimpan.');
    }

    // ---------------------------------------------------------------- Hari libur

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

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Isi cepat libur nasional yang tanggalnya TETAP setiap tahun, pada rentang Tahun Pelajaran.
     * Libur yang berubah tiap tahun (Idul Fitri, Idul Adha, Nyepi, Waisak, dll.)
     * harus ditandai manual sesuai SKB 3 Menteri tahun berjalan.
     */
    public function isiLiburTetap(Request $request)
    {
        $request->validate(['tahun_id' => 'required|exists:tahun_pelajarans,id']);
        $tahun = TahunPelajaran::findOrFail($request->tahun_id);

        $tetap = [
            '01-01' => 'Tahun Baru Masehi',
            '05-01' => 'Hari Buruh Internasional',
            '06-01' => 'Hari Lahir Pancasila',
            '08-17' => 'Hari Kemerdekaan RI',
            '12-25' => 'Hari Raya Natal',
        ];

        $baru = 0;

        for ($y = $tahun->tanggal_mulai->year; $y <= $tahun->tanggal_selesai->year; $y++) {
            foreach ($tetap as $md => $nama) {
                $tgl = Carbon::parse("{$y}-{$md}");

                if ($tgl->lt($tahun->tanggal_mulai) || $tgl->gt($tahun->tanggal_selesai)) {
                    continue;
                }

                if (! HariLibur::whereDate('tanggal', $tgl->toDateString())->exists()) {
                    HariLibur::create(['tanggal' => $tgl->toDateString(), 'keterangan' => $nama, 'jenis' => 'nasional']);
                    $baru++;
                }
            }
        }

        return redirect()->back()->with('success', "{$baru} libur tanggal tetap ditambahkan. Libur yang tanggalnya berubah tiap tahun (mis. Idul Fitri) tandai manual di kalender.");
    }

    public function destroyLibur(HariLibur $libur)
    {
        $libur->delete();

        return redirect()->back()->with('success', 'Hari libur dihapus.');
    }

    // ---------------------------------------------------------------- Periode pembelajaran

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

    // ---------------------------------------------------------------- Helper

    /** Blok kalender per bulan (minggu Senin-Minggu) untuk rentang Tahun Pelajaran. */
    private function bulanBlok(Carbon $mulai, Carbon $selesai, Collection $liburs): array
    {
        $blok   = [];
        $kursor = $mulai->copy()->startOfMonth();

        while ($kursor->lte($selesai)) {
            $awal  = $kursor->copy();
            $akhir = $kursor->copy()->endOfMonth();
            $sel   = array_fill(0, $awal->dayOfWeekIso - 1, null);
            $daftarLibur = [];

            for ($d = $awal->copy(); $d->lte($akhir); $d->addDay()) {
                $tgl   = $d->toDateString();
                $libur = $liburs->get($tgl);
                $luar  = $d->lt($mulai) || $d->gt($selesai);

                if ($libur && ! $luar) {
                    $daftarLibur[] = ['hari' => $d->day, 'keterangan' => $libur->keterangan];
                }

                $sel[] = [
                    'tgl'     => $tgl,
                    'hari'    => $d->day,
                    'libur'   => $libur?->keterangan,
                    'jenis'   => $libur?->jenis,
                    'id'      => $libur?->id,
                    'weekend' => $d->dayOfWeekIso >= 6,
                    'luar'    => $luar,
                ];
            }

            while (count($sel) % 7 !== 0) {
                $sel[] = null;
            }

            $blok[] = [
                'judul'  => AbsenSiswaService::BULAN[$kursor->month] . ' ' . $kursor->year,
                'minggu' => array_chunk($sel, 7),
                'libur'  => $daftarLibur,
            ];

            $kursor->addMonth();
        }

        return $blok;
    }
}
