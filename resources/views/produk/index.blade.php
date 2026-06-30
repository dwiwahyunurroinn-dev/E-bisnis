@extends('layouts.app')

@section('title', config('toko.nama').' — Marketplace Furnitur Kayu Daur Ulang')

@push('styles')
<style>
    .slider { position: relative; border-radius: 22px; overflow: hidden; box-shadow: var(--shadow-md); margin-top: 26px; }
    .slides { display: flex; transition: transform .6s var(--ease); }
    .slide { min-width: 100%; padding: 50px 52px; color: #fff; position: relative; overflow: hidden; }
    .slide-deco { position: absolute; right: 40px; bottom: -30px; color: rgba(255,255,255,.14); animation: float 6s ease-in-out infinite; }
    .slide .s-label { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.2); backdrop-filter: blur(4px); padding: 5px 14px; border-radius: 999px; font-size: .78rem; font-weight: 700; margin-bottom: 12px; }
    .slide h1 { font-size: 2.1rem; font-weight: 800; line-height: 1.18; max-width: 580px; position: relative; }
    .slide p { margin-top: 12px; opacity: .94; max-width: 480px; position: relative; }
    .slide .hero-cta { margin-top: 22px; }
    .slider-dots { position: absolute; bottom: 18px; left: 52px; display: flex; gap: 8px; z-index: 3; }
    .slider-dots button { width: 10px; height: 10px; border-radius: 50%; border: none; background: rgba(255,255,255,.5); cursor: pointer; transition: all .2s; padding: 0; }
    .slider-dots button.on { background: #fff; width: 26px; border-radius: 6px; }
    .slider-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,.22); backdrop-filter: blur(4px); color: #fff; cursor: pointer; z-index: 3; transition: background .2s; display: grid; place-items: center; }
    .slider-arrow:hover { background: rgba(255,255,255,.42); }
    .slider-arrow.prev { left: 16px; } .slider-arrow.next { right: 16px; }

    .pcard-link { display: flex; flex-direction: column; flex: 1; }
    .quick-add { margin: 0 13px 13px; }
    .quick-add button { width: 100%; gap: 6px; padding: 9px; font-size: .82rem; }
    .sortbar { display: flex; align-items: center; gap: 8px; }
    .sortbar select { padding: 8px 12px; border: 1.6px solid var(--line); border-radius: 10px; font-family: inherit; font-size: .85rem; background: #fff; cursor: pointer; }
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
                'label' => null, 'warna' => '#048a55', 'tautan' => '#katalog', 'gambar' => null,
            ]]);
        @endphp
        <div class="slider" id="slider">
            <div class="slides" id="slides">
                @foreach ($daftarSlide as $s)
                    <div class="slide" style="background:linear-gradient(120deg, {{ $s->warna }}, #03734a);
                        @if(!empty($s->gambar)) background-image:linear-gradient(120deg, {{ $s->warna }}cc, #03734acc), url('{{ asset('storage/'.$s->gambar) }}'); background-size:cover; background-position:center; @endif">
                        <span class="slide-deco"><x-icon name="leaf" :size="190"/></span>
                        @if (!empty($s->label))<span class="s-label"><x-icon name="tag" :size="14"/> {{ $s->label }}</span>@endif
                        <h1>{{ $s->judul }}</h1>
                        @if (!empty($s->subjudul))<p>{{ $s->subjudul }}</p>@endif
                        <a href="{{ $s->tautan ?: '#katalog' }}" class="hero-cta">Belanja Sekarang <x-icon name="arrow-right" :size="18"/></a>
                    </div>
                @endforeach
            </div>
            @if ($daftarSlide->count() > 1)
                <button class="slider-arrow prev" onclick="geserSlide(-1)" aria-label="Sebelumnya"><x-icon name="left"/></button>
                <button class="slider-arrow next" onclick="geserSlide(1)" aria-label="Berikutnya"><x-icon name="right"/></button>
                <div class="slider-dots" id="dots">
                    @foreach ($daftarSlide as $i => $s)
                        <button class="{{ $i === 0 ? 'on' : '' }}" onclick="keSlide({{ $i }})" aria-label="Slide {{ $i+1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="trust">
        <div class="trust-item reveal"><span class="t-ico"><x-icon name="recycle" :size="26"/></span><div><b>100% Daur Ulang</b><span>Bahan kayu reclaimed</span></div></div>
        <div class="trust-item reveal"><span class="t-ico"><x-icon name="truck" :size="26"/></span><div><b>Pengiriman Aman</b><span>Dikemas ekstra kuat</span></div></div>
        <div class="trust-item reveal"><span class="t-ico"><x-icon name="shield" :size="26"/></span><div><b>Garansi Kualitas</b><span>Finishing premium</span></div></div>
        <div class="trust-item reveal"><span class="t-ico"><x-icon name="card" :size="26"/></span><div><b>Pembayaran Mudah</b><span>Banyak metode bayar</span></div></div>
    </div>

    <div class="section-head" id="katalog">
        <h2>
            @if (request('q')) Hasil pencarian "{{ request('q') }}"
            @elseif ($kategoriAktif) {{ optional($kategori->firstWhere('slug', $kategoriAktif))->nama ?? 'Kategori' }}
            @else Produk Pilihan @endif
        </h2>
        <form class="sortbar" method="get">
            @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if ($kategoriAktif)<input type="hidden" name="kategori" value="{{ $kategoriAktif }}">@endif
            <span class="sub">{{ $produk->total() }} produk</span>
            <select name="sort" onchange="this.form.submit()">
                <option value="terbaru" {{ request('sort')==='terbaru'?'selected':'' }}>Terbaru</option>
                <option value="termurah" {{ request('sort')==='termurah'?'selected':'' }}>Harga Termurah</option>
                <option value="termahal" {{ request('sort')==='termahal'?'selected':'' }}>Harga Termahal</option>
                <option value="terlaris" {{ request('sort')==='terlaris'?'selected':'' }}>Terlaris</option>
            </select>
        </form>
    </div>

    @if ($produk->isEmpty())
        <div class="empty">
            <x-mascot :size="150" />
            <p style="margin-top:10px;">Belum ada produk yang cocok. Coba kata kunci atau kategori lain.</p>
        </div>
    @else
        <div class="grid">
            @foreach ($produk as $p)
                <div class="pcard reveal">
                    <a href="{{ route('produk.show', $p) }}" class="pcard-link">
                        <div class="pthumb">
                            @if ($p->persenDiskon() > 0)<span class="badge-disc">-{{ $p->persenDiskon() }}%</span>@endif
                            <span class="ph-emoji"><x-icon name="sofa" :size="52"/></span>
                            @if ($p->gambarUrl())
                                <img loading="lazy" decoding="async" src="{{ $p->gambarUrl() }}" alt="{{ $p->nama }}" onerror="this.remove()">
                            @endif
                            @unless ($p->tersedia())<span class="badge-soldout">Stok Habis</span>@endunless
                        </div>
                        <div class="pbody">
                            <span class="pcat">{{ $p->kategori->nama }}</span>
                            <span class="pname">{{ $p->nama }}</span>
                            <span class="pprice">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                            @if ($p->hargaCoret())<span class="pprice-old">Rp{{ number_format($p->hargaCoret(), 0, ',', '.') }}</span>@endif
                            <div class="pmeta">
                                <span class="stars"><x-icon name="star" :size="14"/> {{ $p->ratingTampil() }}</span>
                                <span>·</span><span>{{ $p->terjualTampil() }} terjual</span>
                            </div>
                        </div>
                    </a>
                    @if ($p->tersedia())
                        <form class="quick-add" method="POST" action="{{ route('keranjang.tambah', $p) }}">
                            @csrf
                            <button class="btn btn-outline" type="submit"><x-icon name="cart" :size="16"/> Keranjang</button>
                        </form>
                    @endif
                </div>
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
