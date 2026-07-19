@extends('layouts.admin')

@section('title', 'Pengaturan Toko')

@section('content')
<form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" style="max-width:760px;">
    @csrf

    <div class="panel" style="margin-top:0;">
        <h2>Identitas Toko</h2>
        <div class="two">
            <div class="field">
                <label>Nama Toko</label>
                <input type="text" name="nama" value="{{ old('nama', $nilai['nama'] ?? config('toko.nama')) }}">
                @error('nama')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $nilai['tagline'] ?? config('toko.tagline')) }}">
            </div>
        </div>
        <div class="two">
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $nilai['email'] ?? config('toko.email')) }}">
                @error('email')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon', $nilai['telepon'] ?? config('toko.telepon')) }}">
            </div>
        </div>
        <div class="two">
            <div class="field">
                <label>Nomor WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $nilai['whatsapp'] ?? config('toko.whatsapp')) }}" placeholder="08xxx atau 628xxx">
                <div style="font-size:.76rem;color:var(--ink-soft);margin-top:4px;">Otomatis diubah ke format internasional (62…) untuk tautan wa.me</div>
            </div>
            <div class="field">
                <label>Instagram (tanpa @)</label>
                <input type="text" name="instagram" value="{{ old('instagram', $nilai['instagram'] ?? config('toko.instagram')) }}" placeholder="ecocraft.id">
            </div>
        </div>
        <div class="field">
            <label>Teks Sapaan WhatsApp</label>
            <input type="text" name="whatsapp_text" value="{{ old('whatsapp_text', $nilai['whatsapp_text'] ?? config('toko.whatsapp_text')) }}">
        </div>
    </div>

    <div class="panel">
        <h2>Logo Toko</h2>
        <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
            <div style="width:84px;height:84px;border:1px solid var(--line);border-radius:14px;display:grid;place-items:center;background:var(--bg);overflow:hidden;">
                @if (!empty($nilai['logo']))
                    <img src="{{ asset('storage/'.$nilai['logo']) }}" alt="Logo" style="max-width:100%;max-height:100%;">
                @else
                    <x-logo :size="48" :wordmark="false"/>
                @endif
            </div>
            <div style="flex:1;min-width:240px;">
                <div class="field" style="margin-bottom:8px;">
                    <input type="file" name="logo" accept="image/*">
                    @error('logo')<div class="err">{{ $message }}</div>@enderror
                </div>
                @if (!empty($nilai['logo']))
                    <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;color:var(--ink-soft);">
                        <input type="checkbox" name="hapus_logo" value="1" style="width:15px;height:15px;accent-color:var(--danger);"> Hapus logo unggahan (kembali ke logo bawaan)
                    </label>
                @endif
                <div style="font-size:.76rem;color:var(--ink-soft);margin-top:6px;">PNG/JPG maks 2 MB. Disarankan bentuk persegi.</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Pembayaran</h2>
        <div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;margin-bottom:16px;">
            <div style="width:150px;height:150px;border:1px solid var(--line);border-radius:14px;display:grid;place-items:center;background:var(--bg);overflow:hidden;">
                @if (!empty($nilai['qris_gambar']))
                    <img src="{{ asset('storage/'.$nilai['qris_gambar']) }}" alt="QRIS" style="max-width:100%;max-height:100%;">
                @else
                    <span style="font-size:.76rem;color:var(--ink-soft);text-align:center;padding:8px;">Belum ada barcode QRIS<br>(pakai QR simulasi)</span>
                @endif
            </div>
            <div style="flex:1;min-width:240px;">
                <div class="field" style="margin-bottom:8px;">
                    <label>Barcode QRIS Toko</label>
                    <input type="file" name="qris_gambar" accept="image/*">
                    @error('qris_gambar')<div class="err">{{ $message }}</div>@enderror
                </div>
                @if (!empty($nilai['qris_gambar']))
                    <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;color:var(--ink-soft);">
                        <input type="checkbox" name="hapus_qris" value="1" style="width:15px;height:15px;accent-color:var(--danger);"> Hapus barcode (kembali ke QR simulasi)
                    </label>
                @endif
                <div style="font-size:.76rem;color:var(--ink-soft);margin-top:6px;">Unggah gambar QRIS asli dari penyedia pembayaran Anda. Akan tampil di halaman bayar metode QRIS.</div>
            </div>
        </div>
        <label style="display:block;font-size:.85rem;font-weight:600;margin-bottom:8px;">Nomor Tujuan E-Wallet (tampil di halaman bayar; kosongkan bila tidak dipakai)</label>
        <div class="two" style="margin-bottom:4px;">
            @foreach (['gopay' => ['GoPay', '#00aed6'], 'ovo' => ['OVO', '#4c3494'], 'dana' => ['DANA', '#108ee9'], 'shopeepay' => ['ShopeePay', '#ee4d2d']] as $w => [$labelW, $warnaW])
                <div class="field">
                    <label style="display:inline-flex;align-items:center;gap:7px;">
                        <span style="background:{{ $warnaW }};color:#fff;font-size:.66rem;font-weight:800;padding:3px 8px;border-radius:6px;">{{ $labelW }}</span>
                    </label>
                    <input type="text" name="ewallet_{{ $w }}" value="{{ old('ewallet_'.$w, $nilai['ewallet_'.$w] ?? '') }}" placeholder="08xxx a.n. Nama Toko">
                </div>
            @endforeach
        </div>
        <div class="field">
            <label>Rekening Bank (untuk transfer manual — satu rekening per baris)</label>
            <textarea name="rekening" rows="3" placeholder="BCA 1234567890 a.n. Eco Craft&#10;BRI 0987654321 a.n. Eco Craft">{{ old('rekening', $nilai['rekening'] ?? '') }}</textarea>
            <div style="font-size:.76rem;color:var(--ink-soft);margin-top:4px;">Bila diisi, tampil di halaman pembayaran sebagai opsi transfer manual.</div>
        </div>
    </div>

    <button class="btn btn-primary" style="margin-top:16px;">Simpan Pengaturan</button>
</form>
@endsection
