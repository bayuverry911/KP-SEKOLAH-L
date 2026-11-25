<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use SimpleXMLElement;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('aktif', true)->first()
            ?? TahunPelajaran::orderByDesc('tanggal_mulai')->first();

        $query = Siswa::query()
            ->leftJoin('kelas_siswas as ks', function ($join) use ($tahunAktif) {
                $join->on('siswas.id', '=', 'ks.siswa_id');
                if ($tahunAktif) {
                    $join->where('ks.tahun_pelajaran_id', '=', $tahunAktif->id);
                }
            })
            ->leftJoin('kelas as k', 'k.id', '=', 'ks.kelas_id')
            ->select(
                'siswas.*',
                DB::raw("CASE
                    WHEN ks.status = 'lulus' THEN 'LULUS'
                    WHEN k.nama_kelas IS NOT NULL THEN k.nama_kelas
                    ELSE siswas.kelas
                END as kelas_aktif")
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('siswas.nama', 'like', '%' . $search . '%')
                  ->orWhere('siswas.nisn', 'like', '%' . $search . '%');
            });
        }

        $siswas = $query->latest('siswas.created_at')->paginate(10);
        return view('admin.siswa.index', compact('siswas', 'tahunAktif'));
    }

    public function create()
    {
        $kelases = Kelas::all();
        return view('admin.siswa.create', compact('kelases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn',
            'nama'          => 'required|string|max:255',
            'kelas'         => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        Siswa::create($request->all());

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $siswa   = Siswa::findOrFail($id);
        $kelases = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelases'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn,' . $id,
            'nama'          => 'required|string|max:255',
            'kelas'         => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        $siswa->update($request->all());

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Siswa::findOrFail($id)->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * ============================================================
     * IMPORT MASSAL DATA SISWA DARI FILE EXCEL (.xlsx / .csv)
     * Dilengkapi deteksi duplikat NISN & Nama untuk Toast Peringatan
     * ============================================================
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,csv,txt|max:5120',
        ], [
            'file_excel.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file_excel.mimes'    => 'Format file harus .xlsx atau .csv.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $file = $request->file('file_excel');
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $rows = $ext === 'xlsx' ? $this->bacaXlsx($path) : $this->bacaCsv($path);

        if (count($rows) < 2) {
            return back()->with('tab', 'excel')->with('error', 'File Excel kosong atau hanya berisi header tanpa data siswa.');
        }

        // Deteksi posisi kolom dari baris header (baris ke-1)
        $header   = array_map(fn ($h) => strtolower(trim((string) $h)), $rows[0]);
        $idxNisn  = $this->cariKolom($header, ['nisn']);
        $idxNama  = $this->cariKolom($header, ['nama', 'nama siswa', 'nama lengkap', 'nama_siswa']);
        $idxKelas = $this->cariKolom($header, ['kelas', 'nama_kelas']);
        $idxJk    = $this->cariKolom($header, ['jenis kelamin', 'jenis_kelamin', 'jk', 'kelamin', 'gender']);

        if ($idxNisn === null || $idxNama === null || $idxKelas === null || $idxJk === null) {
            return back()->with('tab', 'excel')->with(
                'error',
                'Header kolom tidak sesuai. Pastikan baris pertama memiliki kolom: NISN, Nama, Kelas, dan Jenis Kelamin.'
            );
        }

        $kelasValid     = Kelas::pluck('nama_kelas')->mapWithKeys(fn ($k) => [strtoupper(trim($k)) => $k])->all();
        $berhasil       = 0;
        $duplikatDaftar = [];
        $gagalBaris     = [];
        $sudahDiFile    = [];

        DB::beginTransaction();
        try {
            foreach (array_slice($rows, 1) as $i => $row) {
                $barisKe = $i + 2;
                $nisn    = trim((string) ($row[$idxNisn] ?? ''));
                $nama    = trim((string) ($row[$idxNama] ?? ''));
                $kelas   = trim((string) ($row[$idxKelas] ?? ''));
                $jkRaw   = strtolower(trim((string) ($row[$idxJk] ?? '')));

                if ($nisn === '' && $nama === '' && $kelas === '' && $jkRaw === '') {
                    continue;
                }

                if ($nisn === '' || $nama === '' || $kelas === '') {
                    $gagalBaris[] = "Baris {$barisKe}: NISN, Nama, atau Kelas kosong";
                    continue;
                }

                $jk = match (true) {
                    in_array($jkRaw, ['l', 'lk', 'laki', 'laki-laki', 'laki laki', 'pria', 'm'], true) => 'Laki-laki',
                    in_array($jkRaw, ['p', 'pr', 'perempuan', 'wanita', 'f'], true) => 'Perempuan',
                    default => null,
                };

                if ($jk === null) {
                    $gagalBaris[] = "Baris {$barisKe} ({$nama}): Jenis kelamin '{$row[$idxJk]}' tidak valid";
                    continue;
                }

                $kunciKombinasi = $nisn . '|' . strtolower($nama);
                if (isset($sudahDiFile[$kunciKombinasi]) || isset($sudahDiFile['nisn:' . $nisn])) {
                    $duplikatDaftar[] = "Baris {$barisKe}: {$nama} (NISN {$nisn}) — ganda di dalam file Excel";
                    continue;
                }

                $siswaAda = Siswa::where('nisn', $nisn)
                    ->orWhere(function ($q) use ($nisn, $nama) {
                        $q->where('nisn', $nisn)
                          ->whereRaw('LOWER(nama) = ?', [strtolower($nama)]);
                    })
                    ->first();

                if ($siswaAda) {
                    $keteranganDuplikat = strtolower(trim($siswaAda->nama)) === strtolower($nama)
                        ? "NISN & Nama sama ({$nama} - {$nisn})"
                        : "NISN {$nisn} sudah terdaftar atas nama {$siswaAda->nama}";

                    $duplikatDaftar[] = "Baris {$barisKe}: {$keteranganDuplikat}";
                    continue;
                }

                $sudahDiFile[$kunciKombinasi] = true;
                $sudahDiFile['nisn:' . $nisn] = true;

                $kelasKey   = strtoupper($kelas);
                $kelasFinal = $kelasValid[$kelasKey] ?? $kelas;

                Siswa::create([
                    'nisn'          => $nisn,
                    'nama'          => $nama,
                    'kelas'         => $kelasFinal,
                    'jenis_kelamin' => $jk,
                ]);
                $berhasil++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('tab', 'excel')->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }

        if ($berhasil === 0 && ! empty($duplikatDaftar)) {
            return back()
                ->with('tab', 'excel')
                ->with('toast_duplikat', [
                    'judul' => 'Data Siswa Sudah Terdaftar (' . count($duplikatDaftar) . ' Duplikat)',
                    'pesan' => 'Tidak ada data baru yang ditambahkan karena NISN dan Nama sudah ada di sistem:',
                    'items' => $duplikatDaftar,
                ]);
        }

        $ringkasan = "Import selesai: {$berhasil} siswa baru berhasil ditambahkan"
            . (! empty($gagalBaris) ? '. Catatan: ' . implode('; ', array_slice($gagalBaris, 0, 3)) : '.');

        $redirect = back()->with('tab', 'excel')->with('success', $ringkasan);

        if (! empty($duplikatDaftar)) {
            $redirect->with('toast_duplikat', [
                'judul' => count($duplikatDaftar) . ' Data Duplikat Dilewati',
                'pesan' => 'NISN dan Nama berikut sudah terdaftar sehingga tidak ditambahkan ulang:',
                'items' => $duplikatDaftar,
            ]);
        }

        return $redirect;
    }

    /**
     * ============================================================
     * UNDUH TEMPLATE EXCEL (.xlsx) SIAP ISI
     * ============================================================
     */
    public function downloadTemplate()
    {
        $contohKelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas')->take(2)->values();
        $k1 = $contohKelas[0] ?? '7A';
        $k2 = $contohKelas[1] ?? $k1;

        $data = [
            ['NISN', 'Nama', 'Kelas', 'Jenis Kelamin'],
            ['0051234567', 'Ahmad Fauzan', $k1, 'Laki-laki'],
            ['0057654321', 'Siti Aisyah', $k2, 'Perempuan'],
        ];

        $tmpFile = tempnam(sys_get_temp_dir(), 'tpl_siswa_') . '.xlsx';
        $this->buatXlsx($tmpFile, $data);

        return response()->download($tmpFile, 'Template_Import_Siswa_SMP_Djojoredjo.xlsx')->deleteFileAfterSend(true);
    }

    private function cariKolom(array $header, array $kandidat): ?int
    {
        foreach ($header as $idx => $kolom) {
            if (in_array($kolom, $kandidat, true)) {
                return $idx;
            }
        }
        return null;
    }

    private function bacaCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = substr_count((string) $firstLine, ';') > substr_count((string) $firstLine, ',') ? ';' : ',';

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function bacaXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $xml = new SimpleXMLElement($ssXml);
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $teks = '';
                    foreach ($si->r as $r) {
                        $teks .= (string) $r->t;
                    }
                    $sharedStrings[] = $teks;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $xml  = new SimpleXMLElement($sheetXml);
        $rows = [];

        foreach ($xml->sheetData->row as $row) {
            $baris = [];
            foreach ($row->c as $c) {
                $ref    = (string) $c['r'];
                $colStr = preg_replace('/[0-9]+/', '', $ref);
                $colIdx = $this->hurufKolomKeIndex($colStr);
                $type   = (string) $c['t'];

                if ($type === 's') {
                    $val = $sharedStrings[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string) ($c->is->t ?? '');
                } else {
                    $val = (string) ($c->v ?? '');
                }

                $baris[$colIdx] = $val;
            }

            if (! empty($baris)) {
                $maxCol = max(array_keys($baris));
                $lengkap = [];
                for ($c = 0; $c <= $maxCol; $c++) {
                    $lengkap[] = $baris[$c] ?? '';
                }
                $rows[] = $lengkap;
            }
        }

        return $rows;
    }

    private function hurufKolomKeIndex(string $huruf): int
    {
        $huruf = strtoupper($huruf);
        $index = 0;
        for ($i = 0; $i < strlen($huruf); $i++) {
            $index = $index * 26 + (ord($huruf[$i]) - 64);
        }
        return max(0, $index - 1);
    }

    private function buatXlsx(string $filepath, array $rows): void
    {
        $zip = new ZipArchive();
        $zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="Data Siswa" sheetId="1" r:id="rId1"/></sheets>
</workbook>');

        $sheetRows = '';
        foreach ($rows as $rIdx => $row) {
            $rNum = $rIdx + 1;
            $cells = '';
            foreach ($row as $cIdx => $val) {
                $colLetter = chr(65 + $cIdx);
                $escaped = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $cells .= "<c r=\"{$colLetter}{$rNum}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
            }
            $sheetRows .= "<row r=\"{$rNum}\">{$cells}</row>";
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $sheetRows . '</sheetData>
</worksheet>');

        $zip->close();
    }
}