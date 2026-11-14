{{-- Filter waktu rekap: Per Bulan | Per Tanggal | Rentang. Dipakai halaman rekap siswa & rekap utama. --}}
<div class="md:col-span-6 w-full" id="filterWaktu">
    <!-- Hidden Mode -->
    <input type="hidden" name="mode" id="fwMode" value="{{ $mode }}">

    <!-- Header & Tabs Segmented Control -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1.5 gap-2">
        <label class="block font-semibold text-slate-600">Pilih Waktu</label>
        
        <div class="flex bg-slate-200/70 p-1 rounded-lg text-[10px] sm:text-xs font-bold w-full sm:w-auto">
            @foreach(['bulan' => 'Per Bulan', 'tanggal' => 'Per Tanggal', 'rentang' => 'Rentang'] as $k => $label)
                <button type="button" data-mode="{{ $k }}"
                        class="fw-tab px-3 py-1.5 rounded-md transition-all duration-200 flex-1 text-center {{ $mode === $k ? 'bg-white text-[#19A389] shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Panel Bulan -->
    <div class="fw-panel {{ $mode === 'bulan' ? '' : 'hidden' }}" data-panel="bulan">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-regular fa-calendar text-slate-400"></i>
            </div>
            <input type="month" name="bulan" value="{{ $bulan }}" {{ $mode === 'bulan' ? '' : 'disabled' }}
                   class="w-full pl-9 p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
        </div>
    </div>

    <!-- Panel Tanggal -->
    <div class="fw-panel {{ $mode === 'tanggal' ? '' : 'hidden' }}" data-panel="tanggal">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-regular fa-calendar-day text-slate-400"></i>
            </div>
            <input type="date" name="tanggal" value="{{ $tanggalPilih }}" {{ $mode === 'tanggal' ? '' : 'disabled' }}
                   class="w-full pl-9 p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
        </div>
    </div>

    <!-- Panel Rentang -->
    <div class="fw-panel {{ $mode === 'rentang' ? '' : 'hidden' }}" data-panel="rentang">
        <div class="space-y-2">
            <!-- Pilihan Periode -->
            <div class="relative">
                <select name="periode" id="fwPeriode" {{ $mode === 'rentang' ? '' : 'disabled' }}
                        class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold text-xs focus:outline-none focus:border-[#19A389] appearance-none cursor-pointer transition">
                    <option value="">-- Pilih Periode / Atur Manual --</option>
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id }}" 
                                data-mulai="{{ $p->tanggal_mulai->toDateString() }}" 
                                data-selesai="{{ $p->tanggal_selesai->toDateString() }}"
                                {{ (string) $periodeId === (string) $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Input Rentang Mulai & Selesai -->
            <div class="flex items-center gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                        <i class="fa-solid fa-play text-slate-400 text-[10px]"></i>
                    </div>
                    <input type="date" name="dari" id="fwDari" value="{{ $dari }}" {{ $mode === 'rentang' ? '' : 'disabled' }}
                           @if(! empty($tahunAktif)) min="{{ $tahunAktif->tanggal_mulai->toDateString() }}" max="{{ $tahunAktif->tanggal_selesai->toDateString() }}" @endif
                           class="w-full pl-7 p-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 text-[11px] focus:outline-none focus:border-[#19A389] transition">
                </div>
                <span class="text-slate-400 font-bold text-[10px]">s/d</span>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                        <i class="fa-solid fa-stop text-slate-400 text-[10px]"></i>
                    </div>
                    <input type="date" name="sampai" id="fwSampai" value="{{ $sampai }}" {{ $mode === 'rentang' ? '' : 'disabled' }}
                           @if(! empty($tahunAktif)) min="{{ $tahunAktif->tanggal_mulai->toDateString() }}" max="{{ $tahunAktif->tanggal_selesai->toDateString() }}" @endif
                           class="w-full pl-7 p-2 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 text-[11px] focus:outline-none focus:border-[#19A389] transition">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var root = document.getElementById('filterWaktu');
        if (!root) return;

        function setMode(m) {
            document.getElementById('fwMode').value = m;
            
            // Atur gaya Tab
            root.querySelectorAll('.fw-tab').forEach(function (t) {
                var on = t.dataset.mode === m;
                t.classList.toggle('bg-white', on);
                t.classList.toggle('text-[#19A389]', on);
                t.classList.toggle('shadow-sm', on);
                t.classList.toggle('text-slate-500', !on);
            });
            
            // Atur tampilan Panel dan disable/enable input agar URL tidak kotor
            root.querySelectorAll('.fw-panel').forEach(function (p) {
                var on = p.dataset.panel === m;
                p.classList.toggle('hidden', !on);
                p.querySelectorAll('input, select').forEach(function (el) { 
                    el.disabled = !on; 
                });
            });
        }

        // Listener klik pada Tab
        root.querySelectorAll('.fw-tab').forEach(function (t) {
            t.addEventListener('click', function () { 
                setMode(t.dataset.mode); 
            });
        });

        // Listener Dropdown Periode untuk otomatis mengisi tanggal Rentang
        var per = document.getElementById('fwPeriode');
        if (per) {
            per.addEventListener('change', function () {
                var o = per.options[per.selectedIndex];
                if (o && o.dataset.mulai) {
                    document.getElementById('fwDari').value = o.dataset.mulai;
                    document.getElementById('fwSampai').value = o.dataset.selesai;
                }
            });
        }
    })();
</script>