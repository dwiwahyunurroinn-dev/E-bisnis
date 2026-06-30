@extends('layouts.app')

@section('title', 'Pesanan Saya — '.config('toko.nama'))

@push('styles')
<style>
    .akun-wrap { max-width: 820px; margin: 28px auto 60px; }
    .akun-tabs { display: flex; gap: 8px; margin-bottom: 18px; }
    .akun-tabs a { padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: .9rem; background: #fff; border: 1px solid var(--line); }
    .akun-tabs a.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .ord { display: flex; align-items: center; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--line); }
    .ord:last-child { border-bottom: none; }
    .ord .meta { flex: 1; }
    .ord .kode { font-weight: 700; }
    .ord .tgl { font-size: .8rem; color: var(--ink-soft); }
    .ord .tot { font-weight: 800; color: var(--primary-deep); }
    .st { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .74rem; font-weight: 700; }
    .st-pending{background:#fdf0d8;color:#9a6a12;} .st-lunas,.st-diproses,.st-dikirim,.st-selesai{background:var(--primary-soft);color:var(--primary-deep);} .st-batal{background:#fde3e1;color:#a3322a;}
</style>
@endpush

@section('content')
<div class="container akun-wrap">
    <div class="akun-tabs">
        <a href="{{ route('akun.profil') }}">Profil</a>
        <a href="{{ route('akun.pesanan') }}" class="active">Pesanan Saya</a>
        <a href="{{ route('notifikasi.index') }}">Notifikasi</a>
    </div>

    <div class="card-panel">
        <h3 style="font-size:1.05rem;font-weight:800;margin-bottom:6px;">Riwayat Pesanan</h3>
        @forelse ($pesanan as $p)
            <div class="ord">
                <span style="color:var(--primary)"><x-icon name="clipboard" :size="26"/></span>
                <div class="meta">
                    <div class="kode">{{ $p->kode }}</div>
                    <div class="tgl">{{ $p->created_at->format('d M Y, H:i') }} · {{ $p->detail->count() }} produk</div>
                </div>
                <div style="text-align:right;">
                    <div class="tot">Rp{{ number_format($p->total, 0, ',', '.') }}</div>
                    <span class="st st-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                </div>
                <a href="{{ route('pesanan.show', $p->kode) }}" class="btn btn-outline btn-block" style="width:auto;padding:9px 16px;">Detail</a>
            </div>
        @empty
            <div class="empty"><x-mascot :size="130" /><p style="margin-top:8px;">Anda belum memiliki pesanan.</p>
            <a href="{{ route('produk.index') }}" class="btn btn-primary" style="margin-top:14px;">Mulai Belanja</a></div>
        @endforelse
        <div style="margin-top:14px;">{{ $pesanan->links() }}</div>
    </div>
</div>
@endsection
