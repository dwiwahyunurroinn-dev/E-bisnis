@extends('layouts.app')

@section('title', 'Buku Alamat — '.config('toko.nama'))

@push('styles')
<style>
    .akun-wrap { max-width: 820px; margin: 28px auto 60px; }
    .akun-tabs { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
    .akun-tabs a { display: inline-flex; align-items: center; gap: 7px; padding: 10px 16px; border-radius: 10px; font-weight: 600; font-size: .88rem; background: #fff; border: 1px solid var(--line); }
    .akun-tabs a.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .addr { border: 1.5px solid var(--line); border-radius: 12px; padding: 14px 16px; margin-bottom: 12px; }
    .addr.utama { border-color: var(--primary); background: var(--primary-mint); }
    .addr .top { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .addr .top b { font-size: .95rem; }
    .tag { background: var(--primary); color: #fff; font-size: .68rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
    .addr .meta { font-size: .86rem; color: var(--ink-soft); }
    .addr .acts { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
    .two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width:640px){ .two{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="container akun-wrap">
    @include('partials.akun-tabs')

    <div class="card-panel" style="margin-bottom:18px;">
        <h3 style="font-size:1.05rem;font-weight:800;margin-bottom:14px;">Alamat Tersimpan</h3>
        @forelse ($alamat as $a)
            <div class="addr {{ $a->utama ? 'utama' : '' }}">
                <div class="top">
                    <b>{{ $a->penerima }}</b>
                    @if ($a->utama)<span class="tag">Utama</span>@endif
                </div>
                <div class="meta">{{ $a->telepon }}<br>{{ $a->alamat_lengkap }}, {{ $a->kota }} {{ $a->kode_pos }}</div>
                <div class="acts">
                    @unless ($a->utama)
                        <form method="POST" action="{{ route('akun.alamat.utama', $a) }}">@csrf<button class="btn btn-outline" style="padding:7px 14px;">Jadikan Utama</button></form>
                    @endunless
                    <form method="POST" action="{{ route('akun.alamat.destroy', $a) }}" onsubmit="return confirm('Hapus alamat ini?')">@csrf @method('DELETE')<button class="btn btn-outline" style="padding:7px 14px;color:var(--danger);border-color:var(--danger);">Hapus</button></form>
                </div>
            </div>
        @empty
            <p style="color:var(--ink-soft);font-size:.9rem;">Belum ada alamat tersimpan. Tambahkan di bawah.</p>
        @endforelse
    </div>

    <div class="card-panel">
        <h3 style="font-size:1.05rem;font-weight:800;margin-bottom:14px;">Tambah Alamat Baru</h3>
        <form method="POST" action="{{ route('akun.alamat.store') }}">
            @csrf
            <div class="two">
                <div class="field"><label>Label (mis. Rumah/Kantor)</label><input type="text" name="label" value="{{ old('label') }}"></div>
                <div class="field"><label>Nama Penerima</label><input type="text" name="penerima" value="{{ old('penerima') }}">@error('penerima')<div class="err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="two">
                <div class="field"><label>No. Telepon</label><input type="text" name="telepon" value="{{ old('telepon') }}">@error('telepon')<div class="err">{{ $message }}</div>@enderror</div>
                <div class="field"><label>Kota / Kabupaten</label><input type="text" name="kota" value="{{ old('kota') }}">@error('kota')<div class="err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="two">
                <div class="field"><label>Kode Pos (opsional)</label><input type="text" name="kode_pos" value="{{ old('kode_pos') }}"></div>
            </div>
            <div class="field"><label>Alamat Lengkap</label><textarea name="alamat_lengkap" rows="3">{{ old('alamat_lengkap') }}</textarea>@error('alamat_lengkap')<div class="err">{{ $message }}</div>@enderror</div>
            <button class="btn btn-primary">Simpan Alamat</button>
        </form>
    </div>
</div>
@endsection
