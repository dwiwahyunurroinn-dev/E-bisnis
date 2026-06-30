@extends('layouts.app')

@section('title', 'Keranjang Belanja — KayuReclaimed')

@push('styles')
<style>
    .cart-wrap2 { display: grid; grid-template-columns: 1.6fr .9fr; gap: 22px; align-items: start; margin: 22px 0 50px; }
    .cart-item { display: flex; gap: 14px; padding: 16px 0; border-bottom: 1px solid var(--line); }
    .cart-item:last-child { border-bottom: none; }
    .ci-thumb { width: 90px; height: 90px; border-radius: 12px; background: linear-gradient(135deg,#eef5f1,#dcebe3); display: grid; place-items: center; font-size: 2rem; flex-shrink: 0; overflow: hidden; position: relative; }
    .ci-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .ci-info { flex: 1; min-width: 0; }
    .ci-info .pcat { font-size: .7rem; }
    .ci-info .nm { font-weight: 600; font-size: .95rem; margin: 2px 0 6px; }
    .ci-info .pr { font-weight: 800; color: var(--primary-deep); }
    .ci-right { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: 10px; }
    .ci-del { background: none; border: none; color: var(--ink-soft); cursor: pointer; font-size: .82rem; transition: color .15s; }
    .ci-del:hover { color: var(--danger); }
    .summary { position: sticky; top: 132px; }
    .summary .row { display: flex; justify-content: space-between; margin: 10px 0; font-size: .92rem; color: var(--ink-soft); }
    .summary .grand { display: flex; justify-content: space-between; margin: 14px 0; padding-top: 14px; border-top: 1.5px dashed var(--line); font-weight: 800; font-size: 1.2rem; color: var(--ink); }
    .summary .grand b { color: var(--primary-deep); }
    @media (max-width: 900px) { .cart-wrap2 { grid-template-columns: 1fr; } .summary { position: static; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="section-head"><h2>Keranjang Belanja</h2><span class="sub">{{ $items->sum('qty') }} item</span></div>

    @if ($items->isEmpty())
        <div class="empty">
            <x-mascot :size="140" />
            <p style="margin-top:8px;">Keranjang Anda masih kosong.</p>
            <a href="{{ route('produk.index') }}" class="btn btn-primary" style="margin-top:18px;">Mulai Belanja</a>
        </div>
    @else
        <div class="cart-wrap2">
            <div class="card-panel reveal">
                @foreach ($items as $i)
                    <div class="cart-item">
                        <a href="{{ route('produk.show', $i['produk']) }}" class="ci-thumb">
                            <span style="color:var(--primary)"><x-icon name="sofa" :size="30"/></span>
                            @if ($i['produk']->gambarUrl())<img loading="lazy" src="{{ $i['produk']->gambarUrl() }}" alt="" onerror="this.remove()">@endif
                        </a>
                        <div class="ci-info">
                            <span class="pcat">{{ $i['produk']->kategori->nama }}</span>
                            <div class="nm">{{ $i['produk']->nama }}</div>
                            <div class="pr">Rp{{ number_format($i['produk']->harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="ci-right">
                            <form method="POST" action="{{ route('keranjang.hapus', $i['produk']) }}">
                                @csrf @method('DELETE')
                                <button class="ci-del" type="submit"> Hapus</button>
                            </form>
                            <form method="POST" action="{{ route('keranjang.ubah', $i['produk']) }}" class="qty">
                                @csrf @method('PATCH')
                                <button type="submit" name="qty" value="{{ $i['qty'] - 1 }}" aria-label="Kurangi">−</button>
                                <input type="text" value="{{ $i['qty'] }}" readonly>
                                <button type="submit" name="qty" value="{{ min($i['qty'] + 1, $i['produk']->stok) }}" aria-label="Tambah">+</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-panel summary reveal">
                <h3>Ringkasan Belanja</h3>
                <div class="row"><span>Subtotal ({{ $items->sum('qty') }} item)</span><span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="row"><span>Ongkir</span><span>Dihitung saat checkout</span></div>
                <div class="grand"><span>Total</span><b>Rp{{ number_format($subtotal, 0, ',', '.') }}</b></div>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">Lanjut ke Checkout →</a>
                <a href="{{ route('produk.index') }}" class="btn btn-outline btn-block" style="margin-top:10px;">Tambah Produk Lain</a>
            </div>
        </div>
    @endif
</div>
@endsection
