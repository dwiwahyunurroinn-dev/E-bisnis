@extends('layouts.app')

@section('title', 'Masuk — '.config('toko.nama'))

@push('styles')
<style>
    .auth-wrap { max-width: 430px; margin: 40px auto 70px; }
    .auth-card { background: var(--white); border-radius: var(--radius); padding: 32px 30px; box-shadow: var(--shadow-md); animation: fadeUp .5s var(--ease) both; }
    .auth-card h1 { font-size: 1.5rem; font-weight: 800; text-align: center; }
    .auth-card .sub { text-align: center; color: var(--ink-soft); font-size: .9rem; margin: 6px 0 22px; }
    .auth-alt { text-align: center; margin-top: 18px; font-size: .9rem; color: var(--ink-soft); }
    .auth-alt a { color: var(--primary); font-weight: 700; }
    .hint { background: var(--primary-mint); border: 1px dashed var(--primary); border-radius: 10px; padding: 10px 14px; font-size: .8rem; color: var(--primary-deep); margin-bottom: 18px; }
    .remember { display: flex; align-items: center; gap: 8px; font-size: .85rem; color: var(--ink-soft); margin-bottom: 16px; }
    .remember input { width: 16px; height: 16px; accent-color: var(--primary); }
</style>
@endpush

@section('content')
<div class="container auth-wrap">
    <div class="auth-card">
        <h1>👋 Selamat Datang</h1>
        <p class="sub">Masuk ke akun {{ config('toko.nama') }} Anda</p>

        <div class="hint">Demo admin: <b>admin@ecocraft.id</b> / <b>password</b></div>

        @if ($errors->any())
            <div class="field"><div class="err">{{ $errors->first() }}</div></div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" autofocus>
            </div>
            <div class="field">
                <label>Kata Sandi</label>
                <input type="password" name="password" placeholder="••••••••">
            </div>
            <label class="remember"><input type="checkbox" name="remember"> Ingat saya</label>
            <button type="submit" class="btn btn-primary btn-block">Masuk →</button>
        </form>

        <p class="auth-alt">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </div>
</div>
@endsection
