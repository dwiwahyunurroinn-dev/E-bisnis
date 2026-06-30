@extends('layouts.admin')

@section('title', $produk->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="panel" style="margin-top:0;max-width:760px;">
    <form method="POST" action="{{ $produk->exists ? route('admin.produk.update', $produk) : route('admin.produk.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($produk->exists) @method('PUT') @endif

        <div class="field">
            <label>Nama Produk</label>
            <input type="text" name="nama" value="{{ old('nama', $produk->nama) }}">
            @error('nama')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="two">
            <div class="field">
                <label>Kategori</label>
                <select name="kategori_id">
                    <option value="">— pilih —</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $produk->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
                @error('kategori_id')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Status</label>
                <select name="status">
                    <option value="aktif" {{ old('status', $produk->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $produk->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="two">
            <div class="field">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value="{{ old('harga', $produk->harga) }}" step="1">
                @error('harga')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Stok</label>
                <input type="number" name="stok" value="{{ old('stok', $produk->stok ?? 0) }}">
                @error('stok')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="two">
            <div class="field">
                <label>Berat (gram)</label>
                <input type="number" name="berat_gram" value="{{ old('berat_gram', $produk->berat_gram ?? 1000) }}">
                @error('berat_gram')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Dimensi</label>
                <input type="text" name="dimensi" value="{{ old('dimensi', $produk->dimensi) }}" placeholder="120 x 60 x 75 cm">
            </div>
        </div>

        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
        </div>

        <div class="field">
            <label>Gambar Produk (dari komputer)</label>
            @if ($produk->gambarUrl())
                <div class="thumb-sm" style="width:90px;height:90px;margin-bottom:8px;"><img src="{{ $produk->gambarUrl() }}" alt=""></div>
            @endif
            <input type="file" name="gambar" accept="image/*">
            @error('gambar')<div class="err">{{ $message }}</div>@enderror
            <small style="color:var(--ink-soft);">Format: JPG/PNG, maks 4 MB. {{ $produk->exists ? 'Kosongkan jika tidak ingin mengganti.' : '' }}</small>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button class="btn btn-primary"> Simpan</button>
            <a href="{{ route('admin.produk.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
