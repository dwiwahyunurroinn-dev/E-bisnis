@extends('layouts.app')

@section('title', 'KayuReclaimed — Marketplace Furnitur Kayu Daur Ulang')

@section('content')
<div class="container">
    <section class="hero">
        <div class="hero-banner">
            <h1>Furnitur Kayu Daur Ulang, Estetik & Ramah Lingkungan</h1>
            <p>Setiap produk dibuat dari kayu reclaimed pilihan — unik, kokoh, dan punya cerita. Belanja aman, kurangi limbah.</p>
            <a href="#katalog" class="hero-cta">Mulai Belanja →</a>
        </div>
    </section>

    <div class="trust">
        <div class="trust-item reveal"><span class="t-ico">♻️</span><div><b>100% Daur Ulang</b><span>Bahan kayu reclaimed</span></div></div>
        <div class="trust-item reveal"><span class="t-ico">🚚</span><div><b>Pengiriman Aman</b><span>Dikemas ekstra kuat</span></div></div>
        <div class="trust-item reveal"><span class="t-ico">🛡️</span><div><b>Garansi Kualitas</b><span>Finishing premium</span></div></div>
        <div class="trust-item reveal"><span class="t-ico">💳</span><div><b>Pembayaran Mudah</b><span>Banyak metode bayar</span></div></div>
    </div>

    <div class="section-head" id="katalog">
        <h2>
            @if (request('q'))
                Hasil pencarian "{{ request('q') }}"
            @elseif ($kategoriAktif)
                {{ optional($kategori->firstWhere('slug', $kategoriAktif))->nama ?? 'Kategori' }}
            @else
                Produk Pilihan
            @endif
        </h2>
        <span class="sub">{{ $produk->total() }} produk ditemukan</span>
    </div>

    @if ($produk->isEmpty())
        <div class="empty">
            <div class="big">🔍</div>
            <p>Belum ada produk yang cocok. Coba kata kunci atau kategori lain.</p>
        </div>
    @else
        <div class="grid">
            @foreach ($produk as $p)
                <a href="{{ route('produk.show', $p) }}" class="pcard reveal">
                    <div class="pthumb">
                        @if ($p->persenDiskon() > 0)
                            <span class="badge-disc">-{{ $p->persenDiskon() }}%</span>
                        @endif
                        <span class="wish" aria-hidden="true">♡</span>
                        <span class="ph-emoji">{{ $p->emoji() }}</span>
                        <img loading="lazy" decoding="async"
                             src="{{ asset('images/produk/'.$p->gambar) }}"
                             alt="{{ $p->nama }}" onerror="this.remove()">
                        @unless ($p->tersedia())
                            <span class="badge-soldout">Stok Habis</span>
                        @endunless
                    </div>
                    <div class="pbody">
                        <span class="pcat">{{ $p->kategori->nama }}</span>
                        <span class="pname">{{ $p->nama }}</span>
                        <span class="pprice">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                        @if ($p->hargaCoret())
                            <span class="pprice-old">Rp{{ number_format($p->hargaCoret(), 0, ',', '.') }}</span>
                        @endif
                        <div class="pmeta">
                            <span class="stars">★ {{ $p->ratingTampil() }}</span>
                            <span>·</span>
                            <span>{{ $p->terjualTampil() }} terjual</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination-wrap">{{ $produk->links() }}</div>
    @endif
</div>
@endsection
