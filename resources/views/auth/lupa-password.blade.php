@extends('layouts.app')

@section('title', 'Lupa Kata Sandi — '.config('toko.nama'))

@push('styles')
<style>
    .auth-wrap { max-width: 430px; margin: 40px auto 70px; }
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
        <div style="text-align:center;margin-bottom:6px;"><x-mascot :size="90" /></div>
        <h1>Lupa Kata Sandi?</h1>
        <p class="sub">Masukkan email akun Anda, kami kirimkan tautan untuk membuat kata sandi baru.</p>

        @if ($errors->any())
            <div class="field"><div class="err">{{ $errors->first() }}</div></div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Kirim Tautan Reset</button>
        </form>

        <p class="auth-alt">Ingat kata sandinya? <a href="{{ route('login') }}">Kembali masuk</a></p>
    </div>
</div>
@endsection
