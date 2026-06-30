@extends('layouts.app')

@section('title', config('toko.nama').' — Marketplace Furnitur Kayu Daur Ulang')

@push('styles')
<style>
    .slider { position: relative; border-radius: 24px; overflow: hidden; box-shadow: var(--shadow-md); margin-top: 26px; }
    .slides { display: flex; transition: transform .6s var(--ease); }
    .slide { min-width: 100%; padding: 50px 52px; color: #fff; position: relative; overflow: hidden; }
    .slide::after { content: "🌿"; position: absolute; right: 50px; bottom: -10px; font-size: 11rem; opacity: .14; animation: float 6s ease-in-out infinite; }
    .slide .s-label { display: inline-block; background: rgba(255,255,255,.2); backdrop-filter: blur(4px); padding: 5px 14px; border-radius: 999px; font-size: .78rem; font-weight: 700; margin-bottom: 12px; }
    .slide h1 { font-size: 2.2rem; font-weight: 800; line-height: 1.18; max-width: 580px; }
    .slide p { margin-top: 12px; opacity: .94; max-width: 480px; }
    .slide .hero-cta { margin-top: 22px; }
    .slider-dots { position: absolute; bottom: 18px; left: 52px; display: flex; gap: 8px; z-index: 3; }
    .slider-dots button { width: 10px; height: 10px; border-radius: 50%; border: none; background: rgba(255,255,255,.5); cursor: pointer; transition: all .2s; padding: 0; }
    .slider-dots button.on { background: #fff; width: 26px; border-radius: 6px; }
    .slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 42px; height: 42px; border-radius: 50%; border: none; background: rgba(255,255,255,.25); backdrop-filter: blur(4px); color: #fff; font-size: 1.3rem; cursor: pointer; z-index: 3; transition: background .2s; }
    .slider-arrow:hover { background: rgba(255,255,255,.45); }
    .slider-arrow.prev { left: 16px; } .slider-arrow.next { right: 16px; }
    @media (max-width: 640px) { .slide { padding: 32px 24px; } .slide h1 { font-size: 1.5rem; } .slider-dots { left: 24px; } }
</style>
@endpush

@section('content')
<div class="container">
    <section class="hero">
        @php
            $daftarSlide = $promos->isNotEmpty() ? $promos : collect([(object) [
                'judul' => 'Furnitur Kayu Daur Ulang, Estetik & Ramah Lingkungan',
                'subjudul' => 'Setiap produk dibuat dari kayu reclaimed pilihan — unik, kokoh, dan punya cerita.',
                'label' => null, 'warna' => '#2c8064', 'tautan' => '#katalog', 'gambar' => null,
            ]]);
        @endphp
        <div class="slider" id="slider">
            <div class="slides" id="slides">
                @foreach ($daftarSlide as $s)
                    <div class="slide" style="background:linear-gradient(120deg, {{ $s->warna }}, #1f6b52);
                        @if(!empty($s->gambar)) background-image:linear-gradient(120deg, {{ $s->warna }}cc, #1f6b52cc), url('{{ asset('storage/'.$s->gambar) }}'); background-size:cover; background-position:center; @endif">
                        @if (!empty($s->label))<span class="s-label">✨ {{ $s->label }}</span>@endif
                        <h1>{{ $s->judul }}</h1>
                        @if (!empty($s->subjudul))<p>{{ $s->subjudul }}</p>@endif
                        <a href="{{ $s->tautan ?: '#katalog' }}" class="hero-cta">Belanja Sekarang →</a>
                    </div>
                @endforeach
            </div>
            @if ($daftarSlide->count() > 1)
                <button class="slider-arrow prev" onclick="geserSlide(-1)" aria-label="Sebelumnya">‹</button>
                <button class="slider-arrow next" onclick="geserSlide(1)" aria-label="Berikutnya">›</button>
                <div class="slider-dots" id="dots">
                    @foreach ($daftarSlide as $i => $s)
                        <button class="{{ $i === 0 ? 'on' : '' }}" onclick="keSlide({{ $i }})" aria-label="Slide {{ $i+1 }}"></button>
                    @endforeach
                </div>
            @endif
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
                        @if ($p->gambarUrl())
                            <img loading="lazy" decoding="async" src="{{ $p->gambarUrl() }}" alt="{{ $p->nama }}" onerror="this.remove()">
                        @endif
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

@section('scripts')
<script>
    (function () {
        const slides = document.getElementById('slides');
        if (!slides) return;
        const total = slides.children.length;
        if (total <= 1) return;
        let idx = 0, timer;
        const dots = document.querySelectorAll('#dots button');
        function render() {
            slides.style.transform = 'translateX(-' + (idx * 100) + '%)';
            dots.forEach((d, i) => d.classList.toggle('on', i === idx));
        }
        window.keSlide = function (i) { idx = (i + total) % total; render(); reset(); };
        window.geserSlide = function (d) { window.keSlide(idx + d); };
        function reset() { clearInterval(timer); timer = setInterval(() => window.geserSlide(1), 5000); }
        reset();
    })();
</script>
@endsection
