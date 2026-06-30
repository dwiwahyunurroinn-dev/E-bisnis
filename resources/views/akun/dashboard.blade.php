@extends('layouts.app')

@section('title', 'Akun Saya — '.config('toko.nama'))

@push('styles')
<style>
    .akun-wrap { max-width: 920px; margin: 28px auto 60px; }
    .akun-tabs { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
    .akun-tabs a { display: inline-flex; align-items: center; gap: 7px; padding: 10px 16px; border-radius: 10px; font-weight: 600; font-size: .88rem; background: #fff; border: 1px solid var(--line); }
    .akun-tabs a.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    .member { background: linear-gradient(120deg, var(--primary), var(--primary-deep)); color: #fff; border-radius: var(--radius); padding: 22px 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; box-shadow: var(--shadow-md); }
    .member .tier { font-size: .78rem; opacity: .9; }
    .member .nm { font-size: 1.3rem; font-weight: 800; }
    .member .poin { text-align: right; }
    .member .poin b { font-size: 1.6rem; }
    .badge-tier { display: inline-block; background: rgba(255,255,255,.22); padding: 3px 12px; border-radius: 999px; font-weight: 700; font-size: .8rem; margin-top: 4px; }
    .acards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 18px 0; }
    .acard { background: #fff; border-radius: var(--radius); padding: 16px; box-shadow: var(--shadow-sm); text-align: center; transition: transform .2s var(--ease); }
    .acard:hover { transform: translateY(-3px); }
    .acard .n { font-size: 1.6rem; font-weight: 800; color: var(--primary-deep); }
    .acard .l { font-size: .8rem; color: var(--ink-soft); margin-top: 4px; }
    .ord { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--line); }
    .ord:last-child { border-bottom: none; }
    .st { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .74rem; font-weight: 700; }
    .st-pending{background:#fdf0d8;color:#9a6a12;} .st-lunas,.st-diproses,.st-dikirim,.st-selesai{background:var(--primary-soft);color:var(--primary-deep);} .st-batal{background:#fde3e1;color:#a3322a;}
    @media (max-width:760px){ .acards{ grid-template-columns:1fr 1fr; } }
</style>
@endpush

@section('content')
<div class="container akun-wrap">
    @include('partials.akun-tabs')

    <div class="member">
        <div>
            <div class="tier">Halo,</div>
            <div class="nm">{{ $user->name }}</div>
            <span class="badge-tier">Member {{ $user->tier() }}</span>
        </div>
        <div class="poin">
            <div style="font-size:.78rem;opacity:.9;">Poin Loyalitas</div>
            <b>{{ number_format($user->poin(), 0, ',', '.') }}</b>
            <div style="font-size:.76rem;opacity:.85;">Total belanja Rp{{ number_format($user->totalBelanja(), 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="acards">
        <div class="acard"><div class="n">{{ $totalPesanan }}</div><div class="l">Total Pesanan</div></div>
        <div class="acard"><div class="n" style="color:var(--accent)">{{ $perluBayar }}</div><div class="l">Perlu Dibayar</div></div>
        <div class="acard"><div class="n">{{ $sedangProses }}</div><div class="l">Sedang Diproses</div></div>
        <div class="acard"><div class="n">{{ $selesai }}</div><div class="l">Selesai</div></div>
    </div>

    <div class="card-panel">
        <h3 style="font-size:1.05rem;font-weight:800;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
            Pesanan Terbaru
            <a href="{{ route('akun.pesanan') }}" class="btn btn-outline" style="margin-left:auto;padding:7px 14px;">Lihat Semua</a>
        </h3>
        @forelse ($pesananTerbaru as $p)
            <div class="ord">
                <span style="color:var(--primary)"><x-icon name="clipboard" :size="24"/></span>
                <div style="flex:1;">
                    <div style="font-weight:700;">{{ $p->kode }}</div>
                    <div style="font-size:.8rem;color:var(--ink-soft);">{{ $p->created_at->format('d M Y') }} · {{ $p->detail->count() }} produk</div>
                </div>
                <span class="st st-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                <a href="{{ route('pesanan.show', $p->kode) }}" class="btn btn-outline" style="padding:8px 14px;">Detail</a>
            </div>
        @empty
            <div class="empty"><x-mascot :size="120" /><p style="margin-top:8px;">Belum ada pesanan.</p>
            <a href="{{ route('produk.index') }}" class="btn btn-primary" style="margin-top:12px;">Mulai Belanja</a></div>
        @endforelse
    </div>
</div>
@endsection
