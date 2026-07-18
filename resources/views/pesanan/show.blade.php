@extends('layouts.app')

@section('title', 'Pesanan '.$pesanan->kode.' — '.config('toko.nama'))

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
    /* Timeline status */
    .timeline { display: flex; justify-content: space-between; position: relative; margin: 8px 0 4px; }
    .timeline::before { content: ""; position: absolute; top: 16px; left: 8%; right: 8%; height: 3px; background: var(--line); z-index: 0; }
    .tl-step { position: relative; z-index: 1; text-align: center; flex: 1; }
    .tl-dot { width: 34px; height: 34px; border-radius: 50%; background: #fff; border: 3px solid var(--line); color: var(--ink-soft); display: grid; place-items: center; margin: 0 auto 6px; }
    .tl-step.done .tl-dot { background: var(--primary); border-color: var(--primary); color: #fff; }
    .tl-step.done .tl-lbl { color: var(--primary-deep); font-weight: 700; }
    .tl-lbl { font-size: .72rem; color: var(--ink-soft); }
    .act-row { display: flex; gap: 10px; flex-wrap: wrap; }
    .act-row > * { flex: 1; min-width: 160px; }
    .btn-wa { background: #25d366; color: #fff; }
    .btn-wa:hover { background: #1da851; }
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

        @if ($pesanan->status !== 'batal')
            @php
                $urut = ['pending' => 0, 'lunas' => 1, 'diproses' => 2, 'dikirim' => 3, 'selesai' => 4];
                $idx = $urut[$pesanan->status] ?? 0;
                $langkah = [['Dibuat','clipboard'],['Dibayar','card'],['Diproses','box'],['Dikirim','truck'],['Selesai','check-circle']];
            @endphp
            <div class="card-panel panel-gap reveal">
                <div class="timeline">
                    @foreach ($langkah as $li => $l)
                        <div class="tl-step {{ $li <= $idx ? 'done' : '' }}">
                            <div class="tl-dot"><x-icon name="{{ $l[1] }}" :size="16"/></div>
                            <div class="tl-lbl">{{ $l[0] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card-panel panel-gap reveal">
            <h3>Detail Pesanan</h3>
            <div class="line"><span class="k">Kode Pesanan</span><span><b>{{ $pesanan->kode }}</b></span></div>
            <div class="line"><span class="k">Tanggal</span><span>{{ $pesanan->created_at->format('d M Y, H:i') }}</span></div>
            <div class="line"><span class="k">Penerima</span><span>{{ $pesanan->penerima }} ({{ $pesanan->telepon }})</span></div>
            <div class="line"><span class="k">Alamat</span><span style="text-align:right;max-width:60%;">{{ $pesanan->alamat_lengkap }}, {{ $pesanan->kota }} {{ $pesanan->kode_pos }}</span></div>
            <div class="line"><span class="k">Pengiriman</span><span>{{ strtoupper($pesanan->kurir) }} — {{ $pesanan->layanan }}</span></div>
            @if ($pesanan->resi)
                <div class="line"><span class="k">No. Resi</span><span><b style="color:var(--primary-deep);letter-spacing:.5px;">{{ $pesanan->resi }}</b></span></div>
            @endif
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
            @if ($pesanan->diskon > 0)
                <div class="line"><span class="k">Diskon{{ $pesanan->kode_voucher ? ' ('.$pesanan->kode_voucher.')' : '' }}</span><span style="color:var(--primary-deep);">− Rp{{ number_format($pesanan->diskon, 0, ',', '.') }}</span></div>
            @endif
            <div class="line"><span class="k">Ongkir</span><span>Rp{{ number_format($pesanan->ongkir, 0, ',', '.') }}</span></div>
            <div class="grand"><span>Total</span><b>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</b></div>
        </div>

        @php $waPesan = urlencode('Halo Admin Eco Craft, saya ingin menanyakan pesanan '.$pesanan->kode); @endphp

        @if ($pesanan->status === 'pending')
            <form method="POST" action="{{ route('pesanan.bayar', $pesanan->kode) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-block"><x-icon name="card" :size="18"/> Bayar Sekarang</button>
            </form>
            <div class="act-row" style="margin-top:10px;">
                <form method="POST" action="{{ route('pesanan.batal', $pesanan->kode) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-block">Batalkan Pesanan</button>
                </form>
                <a href="https://wa.me/{{ config('toko.whatsapp') }}?text={{ $waPesan }}" target="_blank" rel="noopener" class="btn btn-wa"><x-icon name="phone" :size="16"/> Hubungi Admin</a>
            </div>
            <p style="text-align:center;font-size:.78rem;color:var(--ink-soft);margin-top:10px;">
                @if ($midtransAktif) Anda akan diarahkan ke halaman pembayaran aman. @else Mode pembayaran simulasi aktif. @endif
            </p>
        @elseif ($pesanan->status === 'dikirim')
            <form method="POST" action="{{ route('pesanan.terima', $pesanan->kode) }}" onsubmit="return confirm('Konfirmasi pesanan sudah diterima?')">
                @csrf
                <button type="submit" class="btn btn-primary btn-block"><x-icon name="check-circle" :size="18"/> Pesanan Diterima</button>
            </form>
            <a href="https://wa.me/{{ config('toko.whatsapp') }}?text={{ $waPesan }}" target="_blank" rel="noopener" class="btn btn-wa btn-block" style="margin-top:10px;"><x-icon name="phone" :size="16"/> Hubungi Admin (WA)</a>
        @else
            <div class="act-row">
                <a href="{{ route('produk.index') }}" class="btn btn-outline">Kembali Belanja</a>
                <a href="https://wa.me/{{ config('toko.whatsapp') }}?text={{ $waPesan }}" target="_blank" rel="noopener" class="btn btn-wa"><x-icon name="phone" :size="16"/> Hubungi Admin</a>
            </div>
        @endif
    </div>
</div>
@endsection
