@extends('layouts.app')

@section('title', 'Katalog Furnitur — KayuReclaimed')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Furnitur Kayu Daur Ulang</h1>
        <p>Setiap produk dibuat dari kayu reclaimed pilihan — unik, kokoh, dan ramah lingkungan.</p>
    </div>
</section>

<div class="container">
    <div class="chips">
        <a href="{{ route('produk.index') }}"
           class="chip {{ $kategoriAktif ? '' : 'active' }}">Semua</a>
        @foreach ($kategori as $k)
            <a href="{{ route('produk.index', ['kategori' => $k->slug]) }}"
               class="chip {{ $kategoriAktif === $k->slug ? 'active' : '' }}">{{ $k->nama }}</a>
        @endforeach
    </div>

    @if ($produk->isEmpty())
        <p class="empty">Belum ada produk yang cocok dengan pencarian Anda.</p>
    @else
        <div class="grid">
            @foreach ($produk as $p)
                <a href="{{ route('produk.show', $p) }}" class="card">
                    <div class="thumb-wrap">
                        @unless ($p->tersedia())
                            <span class="badge-habis">Stok habis</span>
                        @endunless
                        <img class="thumb" loading="lazy" decoding="async"
                             src="{{ asset('images/produk/'.$p->gambar) }}"
                             alt="{{ $p->nama }}"
                             onerror="this.style.visibility='hidden'">
                    </div>
                    <div class="card-body">
                        <span class="kat">{{ $p->kategori->nama }}</span>
                        <span class="nama">{{ $p->nama }}</span>
                        <span class="harga">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                        <span class="stok {{ $p->tersedia() ? 'ada' : 'habis' }}">
                            {{ $p->tersedia() ? 'Stok: '.$p->stok.' unit' : 'Stok habis' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{ $produk->links() }}
    @endif
</div>
@endsection
