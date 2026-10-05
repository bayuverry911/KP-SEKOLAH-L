<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen p-6 sm:p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Kelola Data Guru</h1>
                    <p class="text-xs text-slate-500">Daftar guru terdaftar di SMP Djojoredjo</p>
                </div>
                <a href="{{ route('admin.guru.create') }}" class="bg-[#24B9A1] hover:bg-[#1da18c] text-white px-4 py-2.5 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-md transition">
                    <i class="fa-solid fa-plus"></i> Tambah Guru
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2 font-medium">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#0C2320] text-white uppercase font-bold">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Nama Guru</th>
                                <th class="p-3.5">NUPTK</th>
                                <th class="p-3.5">JenisKelamin</th>
                                <th class="p-3.5">Mata Pelajaran</th>
                                <th class="p-3.5">No Telepon</th>
                                <th class="p-3.5">Kelas Mengajar</th>
                                <th class="p-3.5 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($gurus as $index => $guru)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3.5 font-semibold text-center text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="p-3.5 font-bold text-slate-800">{{ $guru->nama }}</td>
                                    <td class="p-3.5 text-emerald-600 font-medium">{{ $guru->nuptk ?? $guru->nip ?? '-' }}</td>
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $guru->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $guru->jenis_kelamin == 'L' ? 'Laki-Laki' : ($guru->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 font-medium text-slate-700">{{ $guru->mata_pelajaran ?? '-' }}</td>
                                    <td class="p-3.5">{{ $guru->no_telepon ?? '-' }}</td>
                                    <td class="p-3.5">
                                        @if(!empty($guru->kelas))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(explode(',', $guru->kelas) as $kls)
                                                    <span class="bg-teal-50 text-teal-700 border border-teal-200 px-2 py-0.5 rounded-md font-semibold text-[11px]">
                                                        {{ trim($kls) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-center space-x-1">
                                        <a href="{{ route('admin.guru.edit', $guru->id) }}" title="Edit Data" class="p-2 bg-amber-100 text-amber-600 hover:bg-amber-200 rounded-lg inline-block transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Data" class="p-2 bg-rose-100 text-rose-600 hover:bg-rose-200 rounded-lg transition">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-6 text-center text-slate-400 italic">Belum ada data guru terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>