@extends('layouts.admin')

@section('title', $faq->exists ? 'Edit FAQ' : 'Tambah FAQ')

@section('content')
<div class="panel" style="margin-top:0;max-width:680px;">
    <form method="POST" action="{{ $faq->exists ? route('admin.faq.update', $faq) : route('admin.faq.store') }}">
        @csrf
        @if ($faq->exists) @method('PUT') @endif

        <div class="field">
            <label>Pertanyaan</label>
            <input type="text" name="pertanyaan" value="{{ old('pertanyaan', $faq->pertanyaan) }}" placeholder="Berapa lama pengiriman?">
            @error('pertanyaan')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Jawaban</label>
            <textarea name="jawaban" rows="4" placeholder="Jawaban yang akan dikirim bot ke pelanggan">{{ old('jawaban', $faq->jawaban) }}</textarea>
            @error('jawaban')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Kata Kunci Pemicu (pisahkan dengan koma)</label>
            <input type="text" name="kata_kunci" value="{{ old('kata_kunci', $faq->kata_kunci) }}" placeholder="ongkir, pengiriman, kirim">
            @error('kata_kunci')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="two">
            <div class="field">
                <label>Kategori (opsional)</label>
                <input type="text" name="kategori" value="{{ old('kategori', $faq->kategori) }}" placeholder="Pengiriman">
            </div>
            <div class="field">
                <label>Urutan Tampil</label>
                <input type="number" name="urutan" value="{{ old('urutan', $faq->urutan ?? 0) }}">
            </div>
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:16px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $faq->aktif ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Aktif
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
