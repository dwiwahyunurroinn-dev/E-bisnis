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
    /* Panel instruksi pembayaran (gaya marketplace) */
    .pay-deadline { display: flex; justify-content: space-between; align-items: center; gap: 10px; background: #fdf0d8; color: #9a6a12; border-radius: 10px; padding: 11px 16px; font-size: .85rem; font-weight: 600; margin-bottom: 16px; flex-wrap: wrap; }
    .pay-deadline b { font-variant-numeric: tabular-nums; }
    .pay-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .pay-logo { min-width: 58px; height: 32px; padding: 0 10px; border-radius: 8px; color: #fff; font-size: .72rem; font-weight: 800; display: grid; place-items: center; }
    .va-box { display: flex; justify-content: space-between; align-items: center; gap: 10px; border: 1.6px dashed var(--primary); background: var(--primary-mint); border-radius: 12px; padding: 14px 16px; margin: 10px 0 14px; }
    .va-box .no { font-size: 1.25rem; font-weight: 800; letter-spacing: 1px; color: var(--primary-deep); font-variant-numeric: tabular-nums; }
    .va-box button { border: none; background: var(--primary); color: #fff; font-weight: 700; font-size: .8rem; padding: 8px 14px; border-radius: 9px; cursor: pointer; font-family: inherit; }
    details.cara { border: 1px solid var(--line); border-radius: 10px; margin-bottom: 8px; overflow: hidden; }
    details.cara summary { cursor: pointer; padding: 11px 14px; font-size: .87rem; font-weight: 700; list-style: none; display: flex; justify-content: space-between; }
    details.cara summary::after { content: "▾"; color: var(--ink-soft); }
    details.cara[open] summary::after { content: "▴"; }
    details.cara ol { padding: 0 16px 12px 32px; font-size: .84rem; color: var(--ink-soft); }
    details.cara ol li { margin-bottom: 4px; }
    .qr-wrap { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 6px 0 14px; }
    .qr-wrap .total-qr { font-size: 1.3rem; font-weight: 800; color: var(--primary-deep); }
    .sim-note { text-align: center; font-size: .78rem; color: var(--ink-soft); margin-top: 10px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="order-wrap">
        <div class="order-head">
            @if ($pesanan->status === 'pending')
                <div class="ico" style="color:var(--accent);"><x-icon name="clock" :size="56"/></div>
                <h1>Menunggu Pembayaran</h1>
                <p>Selesaikan pembayaran via <b>{{ $pesanan->labelMetode() }}</b> untuk memproses pesanan Anda.</p>
            @elseif ($pesanan->isCod() && $pesanan->status === 'lunas')
                <div class="ico" style="color:var(--primary);"><x-icon name="check-circle" :size="56"/></div>
                <h1>Pesanan COD Dikonfirmasi</h1>
                <p>Siapkan uang tunai <b>Rp{{ number_format($pesanan->total, 0, ',', '.') }}</b> saat kurir tiba.</p>
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
                $langkah = [['Dibuat','clipboard'],[$pesanan->isCod() ? 'Dikonfirmasi' : 'Dibayar','card'],['Diproses','box'],['Dikirim','truck'],['Selesai','check-circle']];
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
            @if ($pesanan->labelMetode())
                <div class="line"><span class="k">Metode Pembayaran</span><span>{{ $pesanan->labelMetode() }}{{ $pesanan->isCod() ? ' — bayar tunai ke kurir' : '' }}</span></div>
            @endif
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
            @php
                $kanal = $pesanan->kanalBayar() ?? ['label' => 'Pembayaran', 'tipe' => 'qris', 'warna' => '#03734a'];
                $batasBayar = $pesanan->created_at->addHours((int) config('toko.pesanan_expire_jam', 24));
            @endphp

            @if ($midtransAktif)
                <form method="POST" action="{{ route('pesanan.bayar', $pesanan->kode) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block"><x-icon name="card" :size="18"/> Bayar Sekarang</button>
                </form>
                <p style="text-align:center;font-size:.78rem;color:var(--ink-soft);margin-top:10px;">Anda akan diarahkan ke halaman pembayaran aman (Midtrans).</p>
            @else
                <div class="card-panel panel-gap reveal">
                    <div class="pay-deadline">
                        <span>Bayar sebelum <b>{{ $batasBayar->translatedFormat('d M Y, H:i') }}</b></span>
                        <b id="payCountdown" data-batas="{{ $batasBayar->timestamp }}">--:--:--</b>
                    </div>

                    <div class="pay-head">
                        <span class="pay-logo" style="background:{{ $kanal['warna'] }};">{{ $kanal['bank'] ?? strtoupper($pesanan->metode_bayar ?? 'BAYAR') }}</span>
                        <b style="font-size:1rem;">{{ $kanal['label'] }}</b>
                    </div>

                    @if ($kanal['tipe'] === 'qris')
                        <div class="qr-wrap">
                            @include('partials.qris', ['seed' => $pesanan->kode, 'ukuran' => 200])
                            <div class="total-qr">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</div>
                            <p style="font-size:.83rem;color:var(--ink-soft);text-align:center;max-width:380px;">
                                Scan kode QR di atas dengan aplikasi pembayaran apa pun
                                (GoPay, OVO, DANA, ShopeePay, m-banking) lalu konfirmasi.
                            </p>
                        </div>
                    @elseif ($kanal['tipe'] === 'va')
                        <p style="font-size:.85rem;color:var(--ink-soft);">Nomor Virtual Account ({{ $kanal['bank'] }})</p>
                        <div class="va-box">
                            <span class="no" id="nomorVa">{{ $pesanan->nomorVa() }}</span>
                            <button type="button" onclick="salinVa(this)">Salin</button>
                        </div>
                        <div class="line" style="margin-bottom:12px;"><span class="k">Total Pembayaran</span><span><b style="color:var(--primary-deep);">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</b></span></div>
                        <details class="cara">
                            <summary>Cara bayar via m-Banking</summary>
                            <ol>
                                <li>Buka aplikasi m-banking {{ $kanal['bank'] }} Anda.</li>
                                <li>Pilih menu <b>Transfer</b> → <b>Virtual Account</b>.</li>
                                <li>Masukkan nomor VA di atas, lalu periksa nama penerima <b>{{ config('toko.nama') }}</b>.</li>
                                <li>Pastikan nominal sesuai, lalu konfirmasi dengan PIN.</li>
                            </ol>
                        </details>
                        <details class="cara">
                            <summary>Cara bayar via ATM</summary>
                            <ol>
                                <li>Masukkan kartu & PIN di ATM {{ $kanal['bank'] }}.</li>
                                <li>Pilih <b>Transaksi Lainnya</b> → <b>Transfer</b> → <b>Virtual Account</b>.</li>
                                <li>Masukkan nomor VA di atas dan ikuti instruksi di layar.</li>
                                <li>Simpan struk sebagai bukti pembayaran.</li>
                            </ol>
                        </details>
                    @elseif ($kanal['tipe'] === 'ewallet')
                        <div class="line"><span class="k">Total Pembayaran</span><span><b style="color:var(--primary-deep);">Rp{{ number_format($pesanan->total, 0, ',', '.') }}</b></span></div>
                        <p style="font-size:.85rem;color:var(--ink-soft);margin:10px 0 4px;">
                            Klik tombol di bawah — Anda akan diarahkan ke aplikasi {{ $kanal['label'] }}
                            untuk menyelesaikan pembayaran.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('pesanan.bayar', $pesanan->kode) }}" style="margin-top:12px;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">
                            @if ($kanal['tipe'] === 'ewallet') Bayar dengan {{ $kanal['label'] }}
                            @elseif ($kanal['tipe'] === 'va') Saya Sudah Bayar — Cek Status
                            @else Cek Status Pembayaran @endif
                        </button>
                    </form>
                    <p class="sim-note">Mode simulasi aktif — tombol di atas menandai pesanan lunas tanpa transaksi nyata.</p>
                </div>
            @endif

            <div class="act-row" style="margin-top:10px;">
                <form method="POST" action="{{ route('pesanan.batal', $pesanan->kode) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-block">Batalkan Pesanan</button>
                </form>
                <a href="https://wa.me/{{ config('toko.whatsapp') }}?text={{ $waPesan }}" target="_blank" rel="noopener" class="btn btn-wa"><x-icon name="phone" :size="16"/> Hubungi Admin</a>
            </div>
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

@section('scripts')
<script>
    // Hitung mundur batas pembayaran.
    const cd = document.getElementById('payCountdown');
    if (cd) {
        const batas = parseInt(cd.dataset.batas, 10) * 1000;
        const tick = () => {
            let s = Math.max(0, Math.floor((batas - Date.now()) / 1000));
            const j = String(Math.floor(s / 3600)).padStart(2, '0');
            const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
            const d = String(s % 60).padStart(2, '0');
            cd.textContent = j + ':' + m + ':' + d;
        };
        tick();
        setInterval(tick, 1000);
    }

    function salinVa(btn) {
        const no = document.getElementById('nomorVa').textContent.trim();
        navigator.clipboard.writeText(no).then(() => {
            btn.textContent = 'Tersalin ✓';
            setTimeout(() => btn.textContent = 'Salin', 1800);
        });
    }
</script>
@endsection
