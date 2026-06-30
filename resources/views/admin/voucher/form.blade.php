@extends('layouts.admin')

@section('title', $voucher->exists ? 'Edit Voucher' : 'Buat Voucher')

@section('content')
<div class="panel" style="margin-top:0;max-width:620px;">
    <form method="POST" action="{{ $voucher->exists ? route('admin.voucher.update', $voucher) : route('admin.voucher.store') }}">
        @csrf
        @if ($voucher->exists) @method('PUT') @endif

        <div class="field">
            <label>Kode Voucher</label>
            <div style="display:flex;gap:8px;">
                <input type="text" name="kode" id="kode" value="{{ old('kode', $voucher->kode ?? $kodeAcak) }}" style="text-transform:uppercase;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('kode').value='ECO'+Math.random().toString(36).substring(2,7).toUpperCase()">Generate</button>
            </div>
            @error('kode')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="two">
            <div class="field">
                <label>Tipe</label>
                <select name="tipe">
                    <option value="persen" {{ old('tipe', $voucher->tipe) === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                    <option value="nominal" {{ old('tipe', $voucher->tipe) === 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                </select>
            </div>
            <div class="field">
                <label>Nilai</label>
                <input type="number" step="0.01" name="nilai" value="{{ old('nilai', $voucher->nilai) }}" placeholder="10 atau 50000">
                @error('nilai')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="two">
            <div class="field">
                <label>Maks. Potongan (opsional, utk %)</label>
                <input type="number" step="0.01" name="maks_potongan" value="{{ old('maks_potongan', $voucher->maks_potongan) }}">
            </div>
            <div class="field">
                <label>Min. Belanja</label>
                <input type="number" step="0.01" name="min_belanja" value="{{ old('min_belanja', $voucher->min_belanja ?? 0) }}">
            </div>
        </div>

        <div class="two">
            <div class="field">
                <label>Kuota (opsional)</label>
                <input type="number" name="kuota" value="{{ old('kuota', $voucher->kuota) }}" placeholder="kosong = tak terbatas">
            </div>
            <div class="field">
                <label>Tanggal Kedaluwarsa (opsional)</label>
                <input type="date" name="kadaluarsa" value="{{ old('kadaluarsa', $voucher->kadaluarsa?->format('Y-m-d')) }}">
            </div>
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:16px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $voucher->aktif ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Aktif
        </label>

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.voucher.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
