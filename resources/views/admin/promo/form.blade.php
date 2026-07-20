@extends('layouts.admin')

@section('title', $promo->exists ? 'Edit Promo' : 'Tambah Promo')

@section('content')
<div class="panel" style="margin-top:0;max-width:640px;">
    <form method="POST" action="{{ $promo->exists ? route('admin.promo.update', $promo) : route('admin.promo.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($promo->exists) @method('PUT') @endif

        <div class="field">
            <label>Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $promo->judul) }}">
            @error('judul')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label>Subjudul</label>
            <input type="text" name="subjudul" value="{{ old('subjudul', $promo->subjudul) }}">
        </div>
        <div class="two">
            <div class="field">
                <label>Label (badge)</label>
                <input type="text" name="label" value="{{ old('label', $promo->label) }}" placeholder="Diskon 30%">
            </div>
            <div class="field">
                <label>Warna Dasar</label>
                <input type="color" name="warna" value="{{ old('warna', $promo->warna ?? '#2c8064') }}" style="height:44px;padding:4px;">
            </div>
        </div>
        <div class="two">
            <div class="field">
                <label>Tautan Tombol</label>
                <input type="text" name="tautan" value="{{ old('tautan', $promo->tautan) }}" placeholder="/ atau /produk/...">
            </div>
            <div class="field">
                <label>Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $promo->urutan ?? 0) }}" min="0">
            </div>
        </div>
        <div class="field">
            <label>Gambar Banner (opsional)</label>
            @if ($promo->gambar)<div class="thumb-sm" style="width:180px;height:60px;margin-bottom:8px;"><img src="{{ asset('storage/'.$promo->gambar) }}" alt=""></div>@endif
            <input type="file" name="gambar" id="pilihGambar" accept="image/*">
            <input type="hidden" name="gambar_crop" id="gambarCrop">
            <div style="font-size:.78rem;color:var(--ink-soft);margin-top:6px;display:flex;align-items:center;gap:6px;">
                <x-icon name="edit" :size="14"/> Pilih foto — alat penyesuai (geser &amp; zoom agar pas bingkai banner) akan muncul otomatis di bawah.
            </div>
            @error('gambar')<div class="err">{{ $message }}</div>@enderror
            @error('gambar_crop')<div class="err">{{ $message }}</div>@enderror

            {{-- Alat penyesuai foto: geser + zoom agar pas dengan bingkai banner --}}
            <div id="cropWrap" style="display:none;margin-top:12px;">
                <div style="font-size:.82rem;color:var(--ink-soft);margin-bottom:8px;">
                    Sesuaikan foto: <b>geser</b> dengan mouse/jari, <b>zoom</b> dengan slider.
                    Bagian di dalam bingkai inilah yang tampil di banner beranda.
                </div>
                <div id="cropStage"
                     style="position:relative;width:100%;aspect-ratio:3/1;overflow:hidden;border-radius:12px;border:2px dashed var(--primary);background:#eef2f0;cursor:grab;touch-action:none;">
                    <img id="cropImg" alt="" draggable="false"
                         style="position:absolute;left:0;top:0;transform-origin:0 0;user-select:none;pointer-events:none;max-width:none;">
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
                    <span style="font-size:.8rem;color:var(--ink-soft);">Zoom</span>
                    <input type="range" id="cropZoom" min="1" max="3" step="0.01" value="1" style="flex:1;accent-color:var(--primary);">
                    <button type="button" class="btn btn-outline btn-sm" id="cropReset">Reset</button>
                </div>
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:16px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $promo->aktif) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Tampilkan di beranda
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary"> Simpan</button>
            <a href="{{ route('admin.promo.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<script>
(function () {
    const input = document.getElementById('pilihGambar');
    const wrap  = document.getElementById('cropWrap');
    const stage = document.getElementById('cropStage');
    const img   = document.getElementById('cropImg');
    const zoom  = document.getElementById('cropZoom');
    const reset = document.getElementById('cropReset');
    const hasil = document.getElementById('gambarCrop');
    const form  = input.closest('form');

    // Ukuran hasil potong (rasio 3:1, sama dengan bingkai banner beranda).
    const HASIL_W = 1500, HASIL_H = 500;

    let skalaDasar = 1, faktorZoom = 1, posX = 0, posY = 0, adaGambar = false;

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file || !file.type.startsWith('image/')) { wrap.style.display = 'none'; adaGambar = false; return; }
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);
        img.onload = () => {
            wrap.style.display = '';
            // Skala awal: gambar menutupi seluruh bingkai (mode "cover").
            skalaDasar = Math.max(stage.clientWidth / img.naturalWidth, stage.clientHeight / img.naturalHeight);
            faktorZoom = 1; zoom.value = 1;
            posX = (stage.clientWidth  - img.naturalWidth  * skalaDasar) / 2;
            posY = (stage.clientHeight - img.naturalHeight * skalaDasar) / 2;
            adaGambar = true;
            gambarUlang();
        };
    });

    function skala() { return skalaDasar * faktorZoom; }

    function jepit() {
        // Gambar tidak boleh meninggalkan celah kosong di bingkai.
        posX = Math.min(0, Math.max(posX, stage.clientWidth  - img.naturalWidth  * skala()));
        posY = Math.min(0, Math.max(posY, stage.clientHeight - img.naturalHeight * skala()));
    }

    function gambarUlang() {
        jepit();
        img.style.transform = `translate(${posX}px, ${posY}px) scale(${skala()})`;
    }

    // Geser (mouse + sentuh)
    let seret = null;
    stage.addEventListener('pointerdown', e => {
        if (!adaGambar) return;
        seret = { x: e.clientX - posX, y: e.clientY - posY };
        stage.setPointerCapture(e.pointerId);
        stage.style.cursor = 'grabbing';
    });
    stage.addEventListener('pointermove', e => {
        if (!seret) return;
        posX = e.clientX - seret.x;
        posY = e.clientY - seret.y;
        gambarUlang();
    });
    ['pointerup', 'pointercancel'].forEach(ev => stage.addEventListener(ev, () => {
        seret = null; stage.style.cursor = 'grab';
    }));

    zoom.addEventListener('input', () => {
        if (!adaGambar) return;
        // Zoom terhadap titik tengah bingkai agar terasa alami.
        const cx = stage.clientWidth / 2, cy = stage.clientHeight / 2;
        const lama = skala();
        faktorZoom = parseFloat(zoom.value);
        const baru = skala();
        posX = cx - (cx - posX) * (baru / lama);
        posY = cy - (cy - posY) * (baru / lama);
        gambarUlang();
    });

    reset.addEventListener('click', () => {
        faktorZoom = 1; zoom.value = 1;
        posX = (stage.clientWidth  - img.naturalWidth  * skalaDasar) / 2;
        posY = (stage.clientHeight - img.naturalHeight * skalaDasar) / 2;
        gambarUlang();
    });

    // Saat disimpan: potong area bingkai ke kanvas, kirim sebagai base64.
    form.addEventListener('submit', () => {
        if (!adaGambar) return;
        const c = document.createElement('canvas');
        c.width = HASIL_W; c.height = HASIL_H;
        const rasio = HASIL_W / stage.clientWidth;
        c.getContext('2d').drawImage(img,
            posX * rasio, posY * rasio,
            img.naturalWidth * skala() * rasio, img.naturalHeight * skala() * rasio);
        hasil.value = c.toDataURL('image/jpeg', 0.88);
        input.value = ''; // kirim hasil potong saja, bukan file mentah
    });
})();
</script>
@endsection
