{{-- Filter waktu rekap: Per Bulan | Per Tanggal | Rentang. Dipakai halaman rekap siswa & rekap utama. --}}
<div class="md:col-span-6" id="filterWaktu">
    <input type="hidden" name="mode" id="fwMode" value="{{ $mode }}">

    <div class="inline-flex rounded-xl bg-slate-100 p-1 mb-2 text-[11px] font-bold">
        @foreach(['bulan' => 'Per Bulan', 'tanggal' => 'Per Tanggal', 'rentang' => 'Rentang Periode'] as $k => $label)
            <button type="button" data-mode="{{ $k }}"
                    class="fw-tab px-3 py-1.5 rounded-lg transition {{ $mode === $k ? 'bg-white text-[#138A73] shadow' : 'text-slate-500 hover:text-slate-700' }}">{{ $label }}</button>
        @endforeach
    </div>

    <div class="fw-panel {{ $mode === 'bulan' ? '' : 'hidden' }}" data-panel="bulan">
        <input type="month" name="bulan" value="{{ $bulan }}" {{ $mode === 'bulan' ? '' : 'disabled' }}
               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
    </div>

    <div class="fw-panel {{ $mode === 'tanggal' ? '' : 'hidden' }}" data-panel="tanggal">
        <input type="date" name="tanggal" value="{{ $tanggalPilih }}" {{ $mode === 'tanggal' ? '' : 'disabled' }}
               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
    </div>

    <div class="fw-panel {{ $mode === 'rentang' ? '' : 'hidden' }}" data-panel="rentang">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 items-center">
            <select name="periode" id="fwPeriode" {{ $mode === 'rentang' ? '' : 'disabled' }}
                    class="sm:col-span-5 w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                <option value="">- Atur tanggal manual -</option>
                @foreach($periodeList as $p)
                    <option value="{{ $p->id }}" data-mulai="{{ $p->tanggal_mulai->toDateString() }}" data-selesai="{{ $p->tanggal_selesai->toDateString() }}"
                            {{ (string) $periodeId === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                @endforeach
            </select>
            <input type="date" name="dari" id="fwDari" value="{{ $dari }}" {{ $mode === 'rentang' ? '' : 'disabled' }}
                   @if(! empty($tahunAktif)) min="{{ $tahunAktif->tanggal_mulai->toDateString() }}" max="{{ $tahunAktif->tanggal_selesai->toDateString() }}" @endif
                   class="sm:col-span-2 w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
            <span class="sm:col-span-1 text-center font-semibold text-slate-500">s.d.</span>
            <input type="date" name="sampai" id="fwSampai" value="{{ $sampai }}" {{ $mode === 'rentang' ? '' : 'disabled' }}
                   @if(! empty($tahunAktif)) min="{{ $tahunAktif->tanggal_mulai->toDateString() }}" max="{{ $tahunAktif->tanggal_selesai->toDateString() }}" @endif
                   class="sm:col-span-2 w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
        </div>
    </div>
</div>

<script>
    (function () {
        var root = document.getElementById('filterWaktu');
        if (!root) return;

        function setMode(m) {
            document.getElementById('fwMode').value = m;
            root.querySelectorAll('.fw-tab').forEach(function (t) {
                var on = t.dataset.mode === m;
                t.classList.toggle('bg-white', on);
                t.classList.toggle('text-[#138A73]', on);
                t.classList.toggle('shadow', on);
                t.classList.toggle('text-slate-500', !on);
            });
            root.querySelectorAll('.fw-panel').forEach(function (p) {
                var on = p.dataset.panel === m;
                p.classList.toggle('hidden', !on);
                p.querySelectorAll('input,select').forEach(function (el) { el.disabled = !on; });
            });
        }

        root.querySelectorAll('.fw-tab').forEach(function (t) {
            t.addEventListener('click', function () { setMode(t.dataset.mode); });
        });

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
