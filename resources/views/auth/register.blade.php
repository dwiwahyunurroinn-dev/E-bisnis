@extends('layouts.app')

@section('title', 'Daftar — '.config('toko.nama'))

@push('styles')
<style>
    .auth-wrap { max-width: 460px; margin: 40px auto 70px; }
    .auth-card { background: var(--white); border-radius: var(--radius); padding: 32px 30px; box-shadow: var(--shadow-md); animation: fadeUp .5s var(--ease) both; }
    .auth-card h1 { font-size: 1.5rem; font-weight: 800; text-align: center; }
    .auth-card .sub { text-align: center; color: var(--ink-soft); font-size: .9rem; margin: 6px 0 22px; }
    .auth-alt { text-align: center; margin-top: 18px; font-size: .9rem; color: var(--ink-soft); }
    .auth-alt a { color: var(--primary); font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container auth-wrap">
    <div class="auth-card">
        <h1>🌱 Buat Akun Baru</h1>
        <p class="sub">Bergabung dengan {{ config('toko.nama') }}</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" autofocus>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>No. Telepon (opsional)</label>
                <input type="text" name="telepon" value="{{ old('telepon') }}">
            </div>
            <div class="field">
                <label>Kata Sandi</label>
                <input type="password" name="password">
                @error('password')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Ulangi Kata Sandi</label>
                <input type="password" name="password_confirmation">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daftar →</button>
        </form>

        <p class="auth-alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</div>
@endsection
