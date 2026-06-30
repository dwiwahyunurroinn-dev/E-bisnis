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
            @if ($promo->gambar)<div class="thumb-sm" style="width:120px;height:60px;margin-bottom:8px;"><img src="{{ asset('storage/'.$promo->gambar) }}" alt=""></div>@endif
            <input type="file" name="gambar" accept="image/*">
            @error('gambar')<div class="err">{{ $message }}</div>@enderror
        </div>
        <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:16px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $promo->aktif) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Tampilkan di beranda
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('admin.promo.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
