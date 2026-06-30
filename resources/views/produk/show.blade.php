@extends('layouts.app')

@section('title', $produk->nama.' — KayuReclaimed')

@push('styles')
<style>
    .breadcrumb { font-size: .82rem; color: var(--ink-soft); margin: 18px 0 14px; display: flex; gap: 7px; flex-wrap: wrap; }
    .breadcrumb a:hover { color: var(--primary); }
    .detail { display: grid; grid-template-columns: 1fr 1.1fr .85fr; gap: 22px; align-items: start; }
    .detail-gallery { position: sticky; top: 132px; }
    .detail-main-img { aspect-ratio: 1/1; border-radius: var(--radius); background: linear-gradient(135deg,#eef5f1,#dcebe3); display: grid; place-items: center; overflow: hidden; position: relative; box-shadow: var(--shadow-sm); }
    .detail-main-img .ph-emoji { font-size: 6.5rem; opacity: .55; animation: float 6s ease-in-out infinite; }
    .detail-main-img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .detail-info h1 { font-size: 1.5rem; font-weight: 800; line-height: 1.3; }
    .rating-row { display: flex; align-items: center; gap: 10px; margin: 10px 0 14px; font-size: .85rem; color: var(--ink-soft); }
    .price-box { background: linear-gradient(120deg, var(--primary-soft), var(--primary-mint)); border-radius: 14px; padding: 18px 20px; margin: 6px 0 18px; }
    .price-now { font-size: 2rem; font-weight: 800; color: var(--primary-deep); }
    .price-was { font-size: .9rem; color: var(--ink-soft); text-decoration: line-through; margin-left: 8px; }
    .disc-chip { background: var(--danger); color: #fff; font-size: .72rem; font-weight: 700; padding: 3px 9px; border-radius: 6px; margin-left: 8px; }
    .spec { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px dashed var(--line); font-size: .9rem; }
    .spec .k { width: 130px; color: var(--ink-soft); flex-shrink: 0; }
    .eco-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
    .eco-chip { background: var(--primary-soft); color: var(--primary-deep); font-size: .78rem; font-weight: 600; padding: 6px 13px; border-radius: 999px; }
    .desc { margin-top: 18px; font-size: .92rem; color: var(--ink-soft); line-height: 1.75; }
    .buybox { position: sticky; top: 132px; }
    .buybox .label-q { font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 12px; font-weight: 700; }
    .buybox .sumrow { display: flex; justify-content: space-between; align-items: center; margin: 18px 0; font-size: .9rem; }
    .buybox .total { font-weight: 800; font-size: 1.3rem; color: var(--primary-deep); }
    @media (max-width: 900px) { .detail { grid-template-columns: 1fr; } .detail-gallery, .buybox { position: static; } }
</style>
@endpush

@section('content')
<div class="container">
    <nav class="breadcrumb">
        <a href="{{ route('produk.index') }}">Beranda</a> <span>›</span>
        <a href="{{ route('produk.index', ['kategori' => $produk->kategori->slug]) }}">{{ $produk->kategori->nama }}</a>
        <span>›</span> <span>{{ $produk->nama }}</span>
    </nav>

    <div class="detail">
        <div class="detail-gallery reveal">
            <div class="detail-main-img">
                @if ($produk->persenDiskon() > 0)
                    <span class="badge-disc">-{{ $produk->persenDiskon() }}%</span>
                @endif
                <span class="ph-emoji">{{ $produk->emoji() }}</span>
                <img loading="lazy" decoding="async" src="{{ asset('images/produk/'.$produk->gambar) }}" alt="{{ $produk->nama }}" onerror="this.remove()">
            </div>
        </div>

        <div class="detail-info card-panel reveal">
            <span class="pcat">{{ $produk->kategori->nama }}</span>
            <h1>{{ $produk->nama }}</h1>
            <div class="rating-row">
                <span class="stars">★ {{ $produk->ratingTampil() }}</span> <span>·</span>
                <span>{{ $produk->terjualTampil() }} terjual</span> <span>·</span>
                <span>{{ $produk->tersedia() ? 'Stok '.$produk->stok.' unit' : 'Stok habis' }}</span>
            </div>

            <div class="price-box">
                <span class="price-now">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                @if ($produk->hargaCoret())
                    <span class="price-was">Rp{{ number_format($produk->hargaCoret(), 0, ',', '.') }}</span>
                    <span class="disc-chip">Hemat {{ $produk->persenDiskon() }}%</span>
                @endif
            </div>

            <div class="spec"><span class="k">Kategori</span><span>{{ $produk->kategori->nama }}</span></div>
            <div class="spec"><span class="k">Dimensi</span><span>{{ $produk->dimensi ?? '-' }}</span></div>
            <div class="spec"><span class="k">Berat</span><span>{{ number_format($produk->berat_gram / 1000, 1, ',', '.') }} kg</span></div>

            @if ($produk->bahanBaku->isNotEmpty())
                <div style="padding:12px 0;">
                    <span class="k" style="color:var(--ink-soft);font-size:.9rem;">Bahan daur ulang</span>
                    <div class="eco-chips">
                        @foreach ($produk->bahanBaku as $bahan)
                            <span class="eco-chip">♻️ {{ $bahan->nama }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="desc">{{ $produk->deskripsi }}</div>
        </div>

        <form class="buybox card-panel reveal" method="POST" action="{{ route('keranjang.tambah', $produk) }}">
            @csrf
            <div class="label-q">Atur jumlah & beli</div>
            <div class="qty">
                <button type="button" onclick="ubahQty(-1)" aria-label="Kurangi">−</button>
                <input type="text" id="qty" name="qty" value="1" readonly>
                <button type="button" onclick="ubahQty(1)" aria-label="Tambah">+</button>
            </div>
            <div class="sumrow">
                <span>Subtotal</span>
                <span class="total" id="subtotal">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
            </div>
            <button type="submit" name="beli_langsung" value="0" class="btn btn-primary btn-block" {{ $produk->tersedia() ? '' : 'disabled' }}>
                🛒 {{ $produk->tersedia() ? 'Tambah ke Keranjang' : 'Stok Habis' }}
            </button>
            <button type="submit" name="beli_langsung" value="1" class="btn btn-outline btn-block" style="margin-top:10px;" {{ $produk->tersedia() ? '' : 'disabled' }}>
                Beli Langsung
            </button>
        </form>
    </div>

    @if ($terkait->isNotEmpty())
        <div class="section-head"><h2>Produk Serupa</h2></div>
        <div class="grid">
            @foreach ($terkait as $p)
                <a href="{{ route('produk.show', $p) }}" class="pcard reveal">
                    <div class="pthumb">
                        @if ($p->persenDiskon() > 0)<span class="badge-disc">-{{ $p->persenDiskon() }}%</span>@endif
                        <span class="ph-emoji">{{ $p->emoji() }}</span>
                        <img loading="lazy" decoding="async" src="{{ asset('images/produk/'.$p->gambar) }}" alt="{{ $p->nama }}" onerror="this.remove()">
                    </div>
                    <div class="pbody">
                        <span class="pcat">{{ $p->kategori->nama }}</span>
                        <span class="pname">{{ $p->nama }}</span>
                        <span class="pprice">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                        <div class="pmeta"><span class="stars">★ {{ $p->ratingTampil() }}</span> <span>·</span> <span>{{ $p->terjualTampil() }} terjual</span></div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    const HARGA = {{ (int) $produk->harga }};
    const STOK  = {{ (int) $produk->stok }};
    function ubahQty(delta) {
        const el = document.getElementById('qty');
        let v = parseInt(el.value, 10) + delta;
        if (v < 1) v = 1;
        if (STOK > 0 && v > STOK) v = STOK;
        el.value = v;
        document.getElementById('subtotal').textContent = 'Rp' + (HARGA * v).toLocaleString('id-ID');
    }
</script>
@endsection
