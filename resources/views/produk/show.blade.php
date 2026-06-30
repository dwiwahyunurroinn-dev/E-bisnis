@extends('layouts.app')

@section('title', $produk->nama.' — KayuReclaimed')

@section('content')
<div class="container">
    <nav class="breadcrumb">
        <a href="{{ route('produk.index') }}">Beranda</a> <span>›</span>
        <a href="{{ route('produk.index', ['kategori' => $produk->kategori->slug]) }}">{{ $produk->kategori->nama }}</a>
        <span>›</span> <span>{{ $produk->nama }}</span>
    </nav>

    <div class="detail">
        {{-- Galeri --}}
        <div class="detail-gallery">
            <div class="detail-main-img">
                @if ($produk->persenDiskon() > 0)
                    <span class="badge-disc">-{{ $produk->persenDiskon() }}%</span>
                @endif
                <span class="ph-emoji">{{ $produk->emoji() }}</span>
                <img loading="lazy" decoding="async"
                     src="{{ asset('images/produk/'.$produk->gambar) }}"
                     alt="{{ $produk->nama }}" onerror="this.remove()">
            </div>
        </div>

        {{-- Info --}}
        <div class="detail-info">
            <span class="pcat">{{ $produk->kategori->nama }}</span>
            <h1>{{ $produk->nama }}</h1>
            <div class="rating-row">
                <span class="stars">★ {{ $produk->ratingTampil() }}</span>
                <span>·</span>
                <span>{{ $produk->terjualTampil() }} terjual</span>
                <span>·</span>
                <span class="{{ $produk->tersedia() ? '' : '' }}">
                    {{ $produk->tersedia() ? 'Stok '.$produk->stok.' unit' : 'Stok habis' }}
                </span>
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
                <div class="spec" style="border:none; flex-direction:column; align-items:flex-start; gap:8px;">
                    <span class="k">Bahan daur ulang</span>
                    <div class="eco-chips">
                        @foreach ($produk->bahanBaku as $bahan)
                            <span class="eco-chip">♻️ {{ $bahan->nama }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="desc">{{ $produk->deskripsi }}</div>
        </div>

        {{-- Buy box --}}
        <div class="buybox">
            <h3>Atur jumlah & beli</h3>
            <div class="qty">
                <button type="button" onclick="ubahQty(-1)" aria-label="Kurangi">−</button>
                <input type="text" id="qty" value="1" readonly>
                <button type="button" onclick="ubahQty(1)" aria-label="Tambah">+</button>
            </div>
            <div class="row">
                <span>Subtotal</span>
                <span class="total" id="subtotal">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
            </div>
            <button class="btn btn-primary" {{ $produk->tersedia() ? '' : 'disabled' }}>
                🛒 {{ $produk->tersedia() ? 'Tambah ke Keranjang' : 'Stok Habis' }}
            </button>
            <button class="btn btn-outline" {{ $produk->tersedia() ? '' : 'disabled' }}>
                Beli Langsung
            </button>
        </div>
    </div>

    {{-- Produk terkait --}}
    @if ($terkait->isNotEmpty())
        <div class="section-head"><h2>Produk Serupa</h2></div>
        <div class="grid">
            @foreach ($terkait as $p)
                <a href="{{ route('produk.show', $p) }}" class="pcard">
                    <div class="pthumb">
                        @if ($p->persenDiskon() > 0)
                            <span class="badge-disc">-{{ $p->persenDiskon() }}%</span>
                        @endif
                        <span class="ph-emoji">{{ $p->emoji() }}</span>
                        <img loading="lazy" decoding="async"
                             src="{{ asset('images/produk/'.$p->gambar) }}"
                             alt="{{ $p->nama }}" onerror="this.remove()">
                    </div>
                    <div class="pbody">
                        <span class="pcat">{{ $p->kategori->nama }}</span>
                        <span class="pname">{{ $p->nama }}</span>
                        <span class="pprice">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                        <div class="pmeta">
                            <span class="stars">★ {{ $p->ratingTampil() }}</span>
                            <span>·</span><span>{{ $p->terjualTampil() }} terjual</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

<script>
    const HARGA = {{ (int) $produk->harga }};
    const STOK  = {{ (int) $produk->stok }};
    function ubahQty(delta) {
        const el = document.getElementById('qty');
        let v = parseInt(el.value, 10) + delta;
        if (v < 1) v = 1;
        if (STOK > 0 && v > STOK) v = STOK;
        el.value = v;
        document.getElementById('subtotal').textContent =
            'Rp' + (HARGA * v).toLocaleString('id-ID');
    }
</script>
@endsection
