@extends('layouts.app')

@section('title', $produk->nama.' — KayuReclaimed')

@section('content')
<div class="container">
    <div class="detail">
        <div class="thumb-wrap">
            <img loading="lazy" decoding="async"
                 src="{{ asset('images/produk/'.$produk->gambar) }}"
                 alt="{{ $produk->nama }}"
                 onerror="this.src='data:image/svg+xml;utf8,&lt;svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22600%22 height=%22450%22&gt;&lt;rect width=%22100%25%22 height=%22100%25%22 fill=%22%23d8c3a5%22/&gt;&lt;/svg&gt;'">
        </div>
        <div>
            <span class="kat">{{ $produk->kategori->nama }}</span>
            <h1>{{ $produk->nama }}</h1>
            <div class="harga">Rp{{ number_format($produk->harga, 0, ',', '.') }}</div>

            <p class="meta">{{ $produk->deskripsi }}</p>
            <p class="meta"><strong>Dimensi:</strong> {{ $produk->dimensi ?? '-' }}</p>
            <p class="meta">
                <strong>Ketersediaan:</strong>
                <span class="stok {{ $produk->tersedia() ? 'ada' : 'habis' }}">
                    {{ $produk->tersedia() ? $produk->stok.' unit siap kirim' : 'Stok habis' }}
                </span>
            </p>

            <button class="btn" {{ $produk->tersedia() ? '' : 'disabled' }}>
                {{ $produk->tersedia() ? 'Tambah ke Keranjang' : 'Stok Habis' }}
            </button>
        </div>
    </div>

    @if ($terkait->isNotEmpty())
        <h2 style="color:var(--kayu-tua);margin-top:20px;">Produk Serupa</h2>
        <div class="grid">
            @foreach ($terkait as $p)
                <a href="{{ route('produk.show', $p) }}" class="card">
                    <div class="thumb-wrap">
                        <img class="thumb" loading="lazy" decoding="async"
                             src="{{ asset('images/produk/'.$p->gambar) }}"
                             alt="{{ $p->nama }}"
                             onerror="this.style.visibility='hidden'">
                    </div>
                    <div class="card-body">
                        <span class="nama">{{ $p->nama }}</span>
                        <span class="harga">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <a href="{{ route('produk.index') }}" class="back">&larr; Kembali ke katalog</a>
</div>
@endsection
