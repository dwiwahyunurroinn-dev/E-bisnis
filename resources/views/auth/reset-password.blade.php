@extends('layouts.app')

@section('title', 'Reset Kata Sandi — '.config('toko.nama'))

@push('styles')
<style>
    .auth-wrap { max-width: 430px; margin: 40px auto 70px; }
    .auth-card { background: var(--white); border-radius: var(--radius); padding: 32px 30px; box-shadow: var(--shadow-md); animation: fadeUp .5s var(--ease) both; }
    .auth-card h1 { font-size: 1.5rem; font-weight: 800; text-align: center; }
    .auth-card .sub { text-align: center; color: var(--ink-soft); font-size: .9rem; margin: 6px 0 22px; }
</style>
@endpush

@section('content')
<div class="container auth-wrap">
    <div class="auth-card">
        <div style="text-align:center;margin-bottom:6px;"><x-mascot :size="90" /></div>
        <h1>Buat Kata Sandi Baru</h1>
        <p class="sub">Masukkan kata sandi baru untuk akun Anda.</p>

        @if ($errors->any())
            <div class="field"><div class="err">{{ $errors->first() }}</div></div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" placeholder="email@contoh.com">
            </div>
            <div class="field">
                <label>Kata Sandi Baru</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter">
            </div>
            <div class="field">
                <label>Ulangi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Simpan Kata Sandi</button>
        </form>
    </div>
</div>
@endsection
