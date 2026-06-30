@extends('layouts.admin')

@section('title', $bundle->exists ? 'Edit Bundle' : 'Buat Bundle')

@section('content')
<div class="panel" style="margin-top:0;max-width:680px;">
    <form method="POST" action="{{ $bundle->exists ? route('admin.bundle.update', $bundle) : route('admin.bundle.store') }}">
        @csrf
        @if ($bundle->exists) @method('PUT') @endif

        <div class="field">
            <label>Nama Paket</label>
            <input type="text" name="nama" value="{{ old('nama', $bundle->nama) }}" placeholder="mis. Paket Meja + Rak Buku">
            @error('nama')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label>Deskripsi (opsional)</label>
            <textarea name="deskripsi" rows="2">{{ old('deskripsi', $bundle->deskripsi) }}</textarea>
        </div>
        <div class="field">
            <label>Harga Bundle (Rp)</label>
            <input type="number" name="harga_bundle" value="{{ old('harga_bundle', $bundle->harga_bundle) }}">
            @error('harga_bundle')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Pilih Produk dalam Paket (minimal 2)</label>
            @error('produk')<div class="err">{{ $message }}</div>@enderror
            <div style="max-height:280px;overflow-y:auto;border:1.6px solid var(--line);border-radius:10px;padding:8px;">
                @foreach ($semuaProduk as $p)
                    <label style="display:flex;align-items:center;gap:10px;padding:7px 8px;border-radius:8px;cursor:pointer;">
                        <input type="checkbox" name="produk[]" value="{{ $p->id }}" {{ array_key_exists($p->id, $terpilih) || in_array($p->id, old('produk', [])) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);">
                        <span style="flex:1;">{{ $p->nama }}</span>
                        <span style="color:var(--ink-soft);font-size:.85rem;">Rp{{ number_format($p->harga,0,',','.') }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:16px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $bundle->aktif ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Tampilkan di beranda
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.bundle.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
