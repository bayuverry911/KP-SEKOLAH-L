{{-- Komponen UI ringan (tanpa library): toast, konfirmasi, input alasan. Dimuat sekali per halaman. --}}
<style>
    .ui-ov{position:fixed;inset:0;background:rgba(15,23,42,.5);display:none;align-items:center;justify-content:center;z-index:9999;padding:1rem}
    .ui-ov.ui-show{display:flex}
    .ui-box{background:#fff;border-radius:1.25rem;max-width:26rem;width:100%;padding:1.5rem;box-shadow:0 25px 50px -12px rgba(0,0,0,.3);animation:ui-pop .18s ease-out;font-family:inherit}
    .ui-ico{width:2.75rem;height:2.75rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.15rem;margin-bottom:.9rem}
    .ui-t{font-size:1rem;font-weight:800;color:#0f172a;margin:0 0 .35rem}
    .ui-m{font-size:.8rem;line-height:1.5;color:#475569;margin:0;white-space:pre-line}
    .ui-in{width:100%;margin-top:.9rem;padding:.65rem .8rem;border:1px solid #cbd5e1;border-radius:.75rem;font-size:.8rem;background:#f8fafc;outline:none;font-family:inherit}
    .ui-in:focus{border-color:#19A389}
    .ui-err{color:#be123c;font-size:.7rem;margin-top:.35rem;min-height:1rem}
    .ui-act{display:flex;justify-content:flex-end;gap:.6rem;margin-top:1.25rem}
    .ui-btn{border:0;cursor:pointer;border-radius:.75rem;padding:.6rem 1.1rem;font-size:.78rem;font-weight:700;font-family:inherit;transition:filter .15s}
    .ui-btn:hover{filter:brightness(.93)}
    .ui-no{background:#f1f5f9;color:#475569}
    .ui-ok-info{background:#19A389;color:#fff}.ui-ok-warn{background:#f59e0b;color:#fff}.ui-ok-bad{background:#e11d48;color:#fff}
    .ui-wrap{position:fixed;top:1rem;right:1rem;z-index:10000;display:flex;flex-direction:column;gap:.6rem;width:calc(100% - 2rem);max-width:22rem;pointer-events:none}
    .ui-toast{pointer-events:auto;display:flex;gap:.7rem;align-items:flex-start;background:#fff;border-left:4px solid #19A389;border-radius:.9rem;padding:.8rem .9rem;box-shadow:0 10px 25px -5px rgba(15,23,42,.2);animation:ui-in .25s ease-out;font-size:.75rem;line-height:1.45;font-family:inherit}
    .ui-toast b{display:block;color:#0f172a;font-size:.78rem;margin-bottom:.1rem}.ui-toast span{color:#475569}
    .ui-toast.ui-out{opacity:0;transform:translateX(12px);transition:all .25s}
    .ui-x{margin-left:auto;background:none;border:0;color:#94a3b8;cursor:pointer;font-size:.9rem;line-height:1}
    @keyframes ui-pop{from{opacity:0;transform:translateY(8px) scale(.97)}to{opacity:1;transform:none}}
    @keyframes ui-in{from{opacity:0;transform:translateX(14px)}to{opacity:1;transform:none}}
</style>
<script>
(function () {
    if (window.uiToast) return;

    var TIPE = {
        success: { c: '#19A389', i: '\u2714', bg: '#d1fae5', fg: '#047857', t: 'Berhasil' },
        error:   { c: '#e11d48', i: '!',      bg: '#ffe4e6', fg: '#be123c', t: 'Perhatian' },
        warning: { c: '#f59e0b', i: '!',      bg: '#fef3c7', fg: '#b45309', t: 'Peringatan' },
        info:    { c: '#0ea5e9', i: 'i',      bg: '#e0f2fe', fg: '#0369a1', t: 'Informasi' }
    };

    function h(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text != null) e.textContent = text;
        return e;
    }

    // ---------- TOAST ----------
    var wrap;
    window.uiToast = function (pesan, tipe, judul, ms) {
        var t = TIPE[tipe] || TIPE.info;
        if (!wrap) { wrap = h('div', 'ui-wrap'); document.body.appendChild(wrap); }

        var box = h('div', 'ui-toast');
        box.style.borderLeftColor = t.c;
        var ico = h('div', null, t.i);
        ico.style.cssText = 'flex:none;width:1.6rem;height:1.6rem;border-radius:9999px;display:flex;align-items:center;justify-content:center;font-weight:800;background:' + t.bg + ';color:' + t.fg;
        var isi = h('div');
        isi.appendChild(h('b', null, judul || t.t));
        isi.appendChild(h('span', null, pesan));
        var x = h('button', 'ui-x', '\u00d7');
        x.type = 'button';

        function tutup() { box.classList.add('ui-out'); setTimeout(function () { box.remove(); }, 260); }
        x.addEventListener('click', tutup);
        box.append(ico, isi, x);
        wrap.appendChild(box);
        setTimeout(tutup, ms || 6000);
    };

    // ---------- MODAL (konfirmasi / input) ----------
    var ov, ikon, jdl, msg, input, err, btnNo, btnOk, aksiOk;

    function bangun() {
        if (ov) return;
        ov = h('div', 'ui-ov');
        var box = h('div', 'ui-box');
        ikon = h('div', 'ui-ico');
        jdl = h('h3', 'ui-t');
        msg = h('p', 'ui-m');
        input = h('input', 'ui-in'); input.type = 'text';
        err = h('div', 'ui-err');
        var act = h('div', 'ui-act');
        btnNo = h('button', 'ui-btn ui-no', 'Batal'); btnNo.type = 'button';
        btnOk = h('button', 'ui-btn', 'OK'); btnOk.type = 'button';
        act.append(btnNo, btnOk);
        box.append(ikon, jdl, msg, input, err, act);
        ov.appendChild(box);
        document.body.appendChild(ov);

        btnNo.addEventListener('click', tutupModal);
        ov.addEventListener('mousedown', function (e) { if (e.target === ov) tutupModal(); });
        document.addEventListener('keydown', function (e) {
            if (!ov.classList.contains('ui-show')) return;
            if (e.key === 'Escape') tutupModal();
            if (e.key === 'Enter' && e.target !== btnNo) { e.preventDefault(); btnOk.click(); }
        });
        btnOk.addEventListener('click', function () { if (aksiOk) aksiOk(); });
    }

    function tutupModal() { if (ov) ov.classList.remove('ui-show'); aksiOk = null; }

    function tampilModal(o, denganInput, onOk) {
        bangun();
        var v = { info: ['#e0f2fe', '#0369a1', 'ui-ok-info', 'i'], warn: ['#fef3c7', '#b45309', 'ui-ok-warn', '!'], bad: ['#ffe4e6', '#be123c', 'ui-ok-bad', '!'] }[o.varian || 'info'];
        ikon.style.background = v[0]; ikon.style.color = v[1]; ikon.textContent = v[3];
        jdl.textContent = o.judul || 'Konfirmasi';
        msg.textContent = o.pesan || '';
        btnOk.className = 'ui-btn ' + v[2];
        btnOk.textContent = o.ok || 'Ya, Lanjutkan';
        btnNo.textContent = o.batal || 'Batal';
        input.style.display = denganInput ? 'block' : 'none';
        input.value = ''; input.placeholder = o.placeholder || '';
        err.textContent = '';
        aksiOk = function () {
            if (denganInput) {
                var val = input.value.trim();
                if (val.length < (o.min || 1)) { err.textContent = 'Minimal ' + (o.min || 1) + ' karakter.'; input.focus(); return; }
                tutupModal(); onOk(val);
            } else { tutupModal(); onOk(); }
        };
        ov.classList.add('ui-show');
        setTimeout(function () { (denganInput ? input : btnOk).focus(); }, 30);
    }

    window.uiConfirm = function (o, onOk) {
        if (typeof o === 'string') o = { pesan: o };
        tampilModal(o, false, onOk);
    };
    window.uiPrompt = function (o, onOk) { tampilModal(o, true, onOk); };

    // alert() bawaan peramban diganti toast (tidak memblokir halaman)
    window.alert = function (m) { window.uiToast(String(m), 'error'); };

    // ---------- Konfirmasi otomatis lewat atribut data-confirm ----------
    function opsiDari(el) {
        var m = el.dataset.confirm || '';
        var keluar = /keluar|logout/i.test(m), hapus = /hapus|batalkan|tolak/i.test(m);
        return {
            judul: el.dataset.confirmTitle || (keluar ? 'Keluar dari Akun' : (hapus ? 'Konfirmasi Penghapusan' : 'Konfirmasi')),
            pesan: m,
            ok: el.dataset.confirmOk || (keluar ? 'Ya, Keluar' : (hapus ? 'Ya, Lanjutkan' : 'Ya, Lanjutkan')),
            varian: el.dataset.confirmType || (keluar ? 'warn' : (hapus ? 'bad' : 'info'))
        };
    }

    document.addEventListener('click', function (e) {
        var el = e.target.closest ? e.target.closest('[data-confirm]') : null;
        if (!el || el.tagName === 'FORM') return;
        e.preventDefault(); e.stopImmediatePropagation();
        window.uiConfirm(opsiDari(el), function () {
            var f = el.form || el.closest('form');
            if (f) { f.dataset.uiOk = '1'; f.submit(); }
            else if (el.href) { location.href = el.href; }
        });
    }, true);

    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!f || !f.dataset || !f.dataset.confirm || f.dataset.uiOk === '1') return;
        e.preventDefault(); e.stopImmediatePropagation();
        window.uiConfirm(opsiDari(f), function () { f.dataset.uiOk = '1'; f.submit(); });
    }, true);

    // Ubah pola lama onsubmit/onclick="return confirm('...')" menjadi data-confirm
    function upgrade() {
        document.querySelectorAll('[onsubmit*="confirm("],[onclick*="confirm("]').forEach(function (n) {
            ['onsubmit', 'onclick'].forEach(function (a) {
                var v = n.getAttribute(a);
                var m = v && v.match(/^\s*return\s+confirm\((['"])([\s\S]*?)\1\)\s*;?\s*$/);
                if (m) { n.removeAttribute(a); n.dataset.confirm = m[2].replace(/\\n/g, '\n'); }
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', upgrade); else upgrade();
})();
</script>

@php
    // Pesan flash/validasi yang belum punya tampilan di halaman akan dimunculkan sebagai toast
    $uiPesan = [];
    if (session('success')) { $uiPesan[] = ['success', session('success')]; }
    if (session('error')) { $uiPesan[] = ['error', session('error')]; }
    elseif (isset($errors) && $errors->any()) {
        foreach (array_slice($errors->all(), 0, 3) as $uiE) { $uiPesan[] = ['error', $uiE]; }
    }
@endphp
@if(count($uiPesan))
    <div id="ui-flash" hidden data-pesan="{{ json_encode($uiPesan) }}"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('ui-flash');
            if (!el) return;
            var daftar = []; try { daftar = JSON.parse(el.dataset.pesan); } catch (e) {}
            // tunggu sebentar agar banner/popup bawaan halaman sempat tampil, lalu hindari duplikasi
            setTimeout(function () {
                var teks = (document.body.innerText || '');
                daftar.forEach(function (p) {
                    if (teks.indexOf(String(p[1]).trim()) === -1) uiToast(p[1], p[0]);
                });
            }, 500);
        });
    </script>
@endif
