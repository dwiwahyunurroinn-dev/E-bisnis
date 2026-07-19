@extends('layouts.app')

@section('title', $judul.' — '.config('toko.nama'))

@push('styles')
<style>
    .legal-wrap { max-width: 760px; margin: 28px auto 60px; }
    .legal-card { background: var(--white); border-radius: var(--radius); padding: 34px 38px; box-shadow: var(--shadow-sm); }
    .legal-card h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 6px; }
    .legal-card .updated { font-size: .8rem; color: var(--ink-soft); margin-bottom: 22px; }
    .legal-card h2 { font-size: 1.05rem; font-weight: 800; margin: 22px 0 8px; }
    .legal-card p, .legal-card li { font-size: .92rem; color: #3a4552; line-height: 1.7; }
    .legal-card ol, .legal-card ul { padding-left: 22px; margin: 6px 0 12px; }
    .legal-card li { margin-bottom: 5px; }
    .legal-card a { color: var(--primary-dark); font-weight: 600; }
    @media (max-width: 640px) { .legal-card { padding: 24px 20px; } }
</style>
@endpush

@section('content')
<div class="container legal-wrap">
    <div class="legal-card reveal">
        <h1>{{ $judul }}</h1>
        <div class="updated">Berlaku sejak {{ date('d M Y') }} · {{ config('toko.nama') }}</div>
        @include('halaman.konten.'.$slug)
    </div>
</div>
@endsection
