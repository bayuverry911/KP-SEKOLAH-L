<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\TahunPelajaran;
use App\Services\AbsenSiswaService as Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kenaikan kelas & kelulusan.
 * Penempatan dicatat di tabel riwayat `kelas_siswas` per Tahun Pelajaran;
 * kolom siswas.kelas tidak ditimpa sehingga data historis absensi tetap utuh.
 */
class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        $tahunList = TahunPelajaran::orderBy('tanggal_mulai')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get()
            ->sortBy(fn ($k) => $k->nama_kelas, SORT_NATURAL | SORT_FLAG_CASE)->values();

        $tahunAsal = $request->filled('tahun_asal')
            ? $tahunList->firstWhere('id', (int) $request->query('tahun_asal'))
            : null;
        $tahunAsal ??= $tahunList->firstWhere('aktif', true) ?? $tahunList->first();

        $kelasAsal = $request->filled('kelas_asal')
            ? $kelasList->firstWhere('id', (int) $request->query('kelas_asal'))
            : null;

        $siswas = collect();
        $bolehLulus = false;

        if ($tahunAsal && $kelasAsal) {
            $siswas     = Absen::siswaKelasTahun($kelasAsal->nama_kelas, $tahunAsal);
            $bolehLulus = $this->kelasAkhir($kelasAsal->nama_kelas);
        }

        // Tahun tujuan default: tahun berikutnya setelah tahun asal
        $tahunTujuanDefault = $tahunAsal
            ? $tahunList->first(fn ($t) => $t->tanggal_mulai->gt($tahunAsal->tanggal_mulai))
            : null;

        $riwayat = DB::table('kelas_siswas as ks')
            ->join('siswas', 'siswas.id', '=', 'ks.siswa_id')
            ->join('tahun_pelajarans as tp', 'tp.id', '=', 'ks.tahun_pelajaran_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'ks.kelas_id')
            ->orderByDesc('ks.updated_at')
            ->limit(15)
            ->get(['siswas.nama', 'tp.nama as tahun', 'k.nama_kelas', 'ks.status', 'ks.updated_at']);

        return view('admin.kenaikan.index', compact(
            'tahunList', 'kelasList', 'tahunAsal', 'kelasAsal', 'siswas', 'bolehLulus', 'tahunTujuanDefault', 'riwayat'
        ));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'tahun_asal_id'   => 'required|exists:tahun_pelajarans,id',
            'kelas_asal_id'   => 'required|exists:kelas,id',
            'tahun_tujuan_id' => 'required|exists:tahun_pelajarans,id|different:tahun_asal_id',
            'tujuan'          => 'required|string',
            'siswa_ids'       => 'required|array|min:1',
            'siswa_ids.*'     => 'integer',
        ], [
            'siswa_ids.required'        => 'Pilih minimal satu siswa.',
            'tahun_tujuan_id.different' => 'Tahun Pelajaran tujuan harus berbeda dari tahun asal.',
        ]);

        $tahunAsal   = TahunPelajaran::findOrFail($request->tahun_asal_id);
        $tahunTujuan = TahunPelajaran::findOrFail($request->tahun_tujuan_id);
        $kelasAsal   = Kelas::findOrFail($request->kelas_asal_id);

        $lulus = $request->tujuan === 'LULUS';

        if ($lulus) {
            if (! $this->kelasAkhir($kelasAsal->nama_kelas)) {
                return redirect()->back()->with('error', 'Status Lulus hanya untuk kelas akhir (Kelas 9).');
            }
            $kelasTujuan = null;
        } else {
            $kelasTujuan = Kelas::find((int) $request->tujuan);
            if (! $kelasTujuan) {
                return redirect()->back()->with('error', 'Kelas tujuan tidak valid.');
            }
        }

        // Hanya siswa yang memang berada di kelas asal pada tahun asal yang boleh diproses
        $sah = Absen::siswaKelasTahun($kelasAsal->nama_kelas, $tahunAsal)->pluck('id')->all();
        $ids = array_values(array_intersect(array_map('intval', $request->siswa_ids), array_map('intval', $sah)));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada siswa valid yang dipilih.');
        }

        DB::transaction(function () use ($ids, $tahunAsal, $tahunTujuan, $kelasAsal, $kelasTujuan, $lulus) {
            foreach ($ids as $id) {
                // Pastikan riwayat tahun asal tercatat (data historis aman)
                KelasSiswa::firstOrCreate(
                    ['siswa_id' => $id, 'tahun_pelajaran_id' => $tahunAsal->id],
                    ['kelas_id' => $kelasAsal->id, 'status' => 'aktif']
                );

                KelasSiswa::updateOrCreate(
                    ['siswa_id' => $id, 'tahun_pelajaran_id' => $tahunTujuan->id],
                    ['kelas_id' => $lulus ? null : $kelasTujuan->id, 'status' => $lulus ? 'lulus' : 'aktif']
                );
            }
        });

        $jumlah = count($ids);
        $tujuan = $lulus ? 'berstatus LULUS' : "naik ke Kelas {$kelasTujuan->nama_kelas}";

        return redirect()
            ->route('admin.kenaikan.index', ['tahun_asal' => $tahunAsal->id, 'kelas_asal' => $kelasAsal->id])
            ->with('success', "{$jumlah} siswa {$tujuan} pada Tahun Pelajaran {$tahunTujuan->nama}. Riwayat kelas sebelumnya tetap tersimpan.");
    }

    /** Kelas 9 / IX dianggap kelas akhir (boleh berstatus Lulus). */
    private function kelasAkhir(string $nama): bool
    {
        $n = Absen::normalKelas($nama);

        return str_starts_with($n, '9') || str_starts_with($n, 'ix');
    }
}
