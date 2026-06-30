@extends('layouts.app')

@section('title', 'Pesanan '.$pesanan->kode.' — KayuReclaimed')

@push('styles')
<style>
    .order-wrap { max-width: 760px; margin: 24px auto 60px; }
    .order-head { text-align: center; padding: 30px 20px; }
    .order-head .ico { font-size: 3.4rem; margin-bottom: 8px; animation: pop .5s var(--ease); }
    .order-head h1 { font-size: 1.5rem; font-weight: 800; }
    .order-head p { color: var(--ink-soft); margin-top: 6px; }
    .status-pill { display: inline-flex; align-items: center; gap: 7px; padding: 7px 16px; border-radius: 999px; font-weight: 700; font-size: .85rem; margin-top: 12px; }
    .status-pending { background: #fdf0d8; color: #9a6a12; }
    .status-lunas, .status-diproses, .status-dikirim, .status-selesai { background: var(--primary-soft); color: var(--primary-deep); }
    .status-batal { background: #fde3e1; color: #a3322a; }
    .line { display: flex; justify-content: space-between; padding: 9px 0; font-size: .9rem; border-bottom: 1px solid var(--line); }
    .line:last-child { border-bottom: none; }
    .line .k { color: var(--ink-soft); }
    .o-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: .9rem; }
    .grand { display: flex; justify-content: space-between; padding-top: 14px; margin-top: 6px; border-top: 1.5px dashed var(--line); font-weight: 800; font-size: 1.25rem; }
    .grand b { color: var(--primary-deep); }
    .panel-gap { margin-bottom: 18px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="order-wrap">
        <div class="order-head">
            @if ($pesanan->status === 'pending')
                <div class="ico" style="color:var(--accent);"><x-icon name="clock" :size="56"/></div>
                <h1>Pesanan Berhasil Dibuat</h1>
                <p>Selesaikan pembayaran untuk memproses pesanan Anda.</p>
            @else
                <div class="ico" style="color:var(--primary);"><x-icon name="check-circle" :size="56"/></div>
                <h1>Pembayaran Berhasil</h1>
                <p>Terima kasih! Pesanan Anda sedang kami siapkan.</p>
            @endif
            <div class="status-pill status-{{ $pesanan->status }}">● {{ ucfirst($pesanan->status) }}</div>
        </div>

        <div class="card-panel panel-gap reveal">
            <h3>Detail Pesanan</h3>
            <div class="line"><span class="k">Kode Pesanan</span><span><b>{{ $pesanan->kode }}</b></span></div>
            <div class="line"><span class="k">Tanggal</span><span>{{ $pesanan->created_at->format('d M Y, H:i') }}</span></div>
            <div class="line"><span class="k">Penerima</span><span>{{ $pesanan->alamat->penerima }} ({{ $pesanan->alamat->telepon }})</span></div>
            <div class="line"><span class="k">Alamat</span><span style="text-align:right;max-width:60%;">{{ $pesanan->alamat->alamat_lengkap }}, {{ $pesanan->alamat->kota }}</span></div>
            <div class="line"><span class="k">Pengiriman</span><span>{{ strtoupper($pesanan->kurir) }} — {{ $pesanan->layanan }}</span></div>
        </div>

        <div class="card-panel panel-gap reveal">
            <h3>Produk</h3>
            @foreach ($pesanan->detail as $d)
                <div class="o-item">
                    <span>{{ $d->nama_produk }} <span style="color:var(--ink-soft);">×{{ $d->jumlah }}</span></span>
                    <span>Rp{{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="line" style="margin-top:10px;"><span class="k">Subtotal</span><span>Rp{{ number_format($pesanan->subtotal, 0, ',', '.') }}</span></div>
            <div class="line"><span class="k">Ongkir</span><span>Rp{{ number_format($pesanan->ongkir, 0, ',', '.') }}</span></div>
            <div class="grand"><span>Total</span><b>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</b></div>
        </div>

        @if ($pesanan->status === 'pending')
            <form method="POST" action="{{ route('pesanan.bayar', $pesanan->kode) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-block"><x-icon name="card" :size="18"/> Bayar Sekarang</button>
            </form>
            <p style="text-align:center;font-size:.78rem;color:var(--ink-soft);margin-top:10px;">
                Pembayaran ini tersimulasi. Saat status menjadi <b>Lunas</b>, stok produk otomatis berkurang lewat trigger database.
            </p>
        @else
            <a href="{{ route('produk.index') }}" class="btn btn-outline btn-block">Kembali Belanja</a>
        @endif
    </div>
</div>
@endsection
