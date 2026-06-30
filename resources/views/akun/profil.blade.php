@extends('layouts.app')

@section('title', 'Profil Saya — '.config('toko.nama'))

@push('styles')
<style>
    .akun-wrap { max-width: 720px; margin: 28px auto 60px; }
    .akun-tabs { display: flex; gap: 8px; margin-bottom: 18px; }
    .akun-tabs a { padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: .9rem; background: #fff; border: 1px solid var(--line); }
    .akun-tabs a.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .card-panel + .card-panel { margin-top: 18px; }
</style>
@endpush

@section('content')
<div class="container akun-wrap">
    <div class="akun-tabs">
        <a href="{{ route('akun.profil') }}" class="active">Profil</a>
        <a href="{{ route('akun.pesanan') }}">Pesanan Saya</a>
        <a href="{{ route('notifikasi.index') }}">Notifikasi</a>
    </div>

    <div class="card-panel">
        <h3 style="display:flex;align-items:center;gap:8px;font-size:1.05rem;font-weight:800;margin-bottom:16px;"><x-icon name="user"/> Data Profil</h3>
        <form method="POST" action="{{ route('akun.profil.update') }}">
            @csrf @method('PATCH')
            <div class="field"><label>Nama</label><input type="text" name="name" value="{{ old('name', $user->name) }}">@error('name')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}">@error('email')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>No. Telepon</label><input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}"></div>
            <button class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <div class="card-panel">
        <h3 style="display:flex;align-items:center;gap:8px;font-size:1.05rem;font-weight:800;margin-bottom:16px;"><x-icon name="key"/> Ubah Kata Sandi</h3>
        <form method="POST" action="{{ route('akun.password') }}">
            @csrf @method('PATCH')
            <div class="field"><label>Kata Sandi Lama</label><input type="password" name="password_lama">@error('password_lama')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>Kata Sandi Baru</label><input type="password" name="password">@error('password')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>Ulangi Kata Sandi Baru</label><input type="password" name="password_confirmation"></div>
            <button class="btn btn-outline">Ubah Kata Sandi</button>
        </form>
    </div>
</div>
@endsection
