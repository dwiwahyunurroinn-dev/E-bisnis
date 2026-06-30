@extends('layouts.app')

@section('title', 'Checkout — '.config('toko.nama'))

@push('styles')
<style>
    .co-grid { display: grid; grid-template-columns: 1.5fr .9fr; gap: 22px; align-items: start; margin: 22px 0 50px; }
    .co-grid .stack > * { margin-bottom: 18px; }
    .ship-opt { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1.6px solid var(--line); border-radius: 12px; cursor: pointer; margin-bottom: 10px; transition: border-color .15s, background .15s; }
    .ship-opt:hover { border-color: var(--primary); background: var(--primary-mint); }
    .ship-opt input { accent-color: var(--primary); width: 18px; height: 18px; }
    .ship-opt.sel { border-color: var(--primary); background: var(--primary-soft); }
    .ship-opt .so-main { flex: 1; }
    .ship-opt .so-main b { font-size: .92rem; }
    .ship-opt .so-main span { display: block; font-size: .78rem; color: var(--ink-soft); }
    .ship-opt .so-price { font-weight: 800; color: var(--primary-deep); }
    .mini-item { display: flex; justify-content: space-between; font-size: .85rem; padding: 7px 0; color: var(--ink-soft); }
    .mini-item b { color: var(--ink); font-weight: 600; }
    .summary { position: sticky; top: 132px; }
    .summary .row { display: flex; justify-content: space-between; margin: 10px 0; font-size: .92rem; color: var(--ink-soft); }
    .summary .grand { display: flex; justify-content: space-between; margin: 14px 0; padding-top: 14px; border-top: 1.5px dashed var(--line); font-weight: 800; font-size: 1.25rem; }
    .summary .grand b { color: var(--primary-deep); }
    .two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width: 900px) { .co-grid { grid-template-columns: 1fr; } .summary { position: static; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="section-head"><h2> Checkout</h2></div>

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <div class="co-grid">
            <div class="stack">
                {{-- Alamat --}}
                <div class="card-panel reveal">
                    <h3>Alamat Pengiriman</h3>
                    <p style="font-size:.82rem;color:var(--ink-soft);margin-bottom:14px;">Akun: <b>{{ $user->name }}</b> ({{ $user->email }})</p>
                    <div class="two">
                        <div class="field">
                            <label>Nama Penerima</label>
                            <input type="text" name="nama" value="{{ old('nama', $alamatTerakhir->penerima ?? $user->name) }}" placeholder="Nama lengkap">
                            @error('nama')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>No. Telepon</label>
                            <input type="text" name="telepon" value="{{ old('telepon', $alamatTerakhir->telepon ?? $user->telepon) }}" placeholder="08xxxxxxxxxx">
                            @error('telepon')<div class="err">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="two">
                        <div class="field">
                            <label>Kota / Kabupaten</label>
                            <input type="text" name="kota" id="kota" value="{{ old('kota', $alamatTerakhir->kota ?? '') }}" placeholder="mis. Jakarta">
                            @error('kota')<div class="err">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Kode Pos (opsional)</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $alamatTerakhir->kode_pos ?? '') }}" placeholder="12345">
                        </div>
                    </div>
                    <div class="field">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="3" placeholder="Jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('alamat_lengkap', $alamatTerakhir->alamat_lengkap ?? '') }}</textarea>
                        @error('alamat_lengkap')<div class="err">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Pengiriman --}}
                <div class="card-panel reveal">
                    <h3> Metode Pengiriman</h3>
                    <p style="font-size:.82rem;color:var(--ink-soft);margin-bottom:14px;">Berat total: {{ number_format($beratGram / 1000, 1, ',', '.') }} kg · ongkir dihitung otomatis.</p>
                    @error('pengiriman')<div class="err" style="margin-bottom:10px;">{{ $message }}</div>@enderror
                    @foreach ($opsiOngkir as $idx => $o)
                        <label class="ship-opt {{ $idx === 0 ? 'sel' : '' }}">
                            <input type="radio" name="pengiriman" value="{{ $o['kurir'].'|'.$o['layanan'] }}"
                                   data-ongkir="{{ $o['ongkir'] }}" {{ $idx === 0 ? 'checked' : '' }}>
                            <div class="so-main">
                                <b>{{ $o['nama'] }} — {{ $o['layanan'] }}</b>
                                <span>Estimasi {{ $o['etd'] }}</span>
                            </div>
                            <div class="so-price">Rp{{ number_format($o['ongkir'], 0, ',', '.') }}</div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="card-panel summary reveal">
                <h3> Ringkasan Pesanan</h3>
                @foreach ($items as $i)
                    <div class="mini-item">
                        <span>{{ $i['produk']->nama }} <b>×{{ $i['qty'] }}</b></span>
                        <span>Rp{{ number_format($i['subtotal'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="row" style="margin-top:14px;"><span>Subtotal</span><span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span></div>
                @if ($namaBundle)
                    <div class="row" style="color:var(--accent);"><span>Paket: {{ $namaBundle }}</span><span></span></div>
                @endif
                @if ($diskon > 0)
                    <div class="row" style="color:var(--primary-deep);font-weight:700;"><span>Diskon{{ $voucher ? ' ('.$voucher->kode.')' : '' }}</span><span>− Rp{{ number_format($diskon, 0, ',', '.') }}</span></div>
                @endif
                <div class="row"><span>Ongkir</span><span id="ongkirLabel">Rp{{ number_format($opsiOngkir[0]['ongkir'], 0, ',', '.') }}</span></div>
                <div class="grand"><span>Total</span><b id="totalLabel">Rp{{ number_format(max(0,$subtotal-$diskon) + $opsiOngkir[0]['ongkir'], 0, ',', '.') }}</b></div>
                <button type="submit" class="btn btn-primary btn-block">Buat Pesanan & Bayar <x-icon name="arrow-right" :size="16"/></button>
    </div>
    {{-- Voucher (form terpisah agar tidak submit pesanan) --}}
    <div class="card-panel" style="margin-top:14px;">
        <h3 style="font-size:.95rem;font-weight:800;margin-bottom:10px;">Punya Voucher?</h3>
        @if ($voucher)
            <div style="display:flex;justify-content:space-between;align-items:center;background:var(--primary-soft);padding:10px 14px;border-radius:10px;">
                <span style="font-weight:700;color:var(--primary-deep);">{{ $voucher->kode }} diterapkan</span>
                <button form="lepasVoucher" class="btn btn-outline" style="padding:6px 12px;">Lepas</button>
            </div>
        @else
            <div style="display:flex;gap:8px;">
                <input form="pasangVoucher" type="text" name="kode" placeholder="Masukkan kode" style="flex:1;padding:10px 13px;border:1.6px solid var(--line);border-radius:10px;font-family:inherit;text-transform:uppercase;">
                <button form="pasangVoucher" class="btn btn-primary">Pakai</button>
            </div>
        @endif
            </div>
        </div>
    </form>

    {{-- Form voucher (terpisah dari form checkout) --}}
    <form id="pasangVoucher" method="POST" action="{{ route('voucher.pasang') }}">@csrf</form>
    <form id="lepasVoucher" method="POST" action="{{ route('voucher.lepas') }}">@csrf @method('DELETE')</form>
</div>
@endsection

@section('scripts')
<script>
    const BASE = {{ (int) max(0, $subtotal - $diskon) }};  // subtotal setelah diskon
    function fmt(n) { return 'Rp' + n.toLocaleString('id-ID'); }
    document.querySelectorAll('input[name="pengiriman"]').forEach(r => {
        r.addEventListener('change', function () {
            document.querySelectorAll('.ship-opt').forEach(o => o.classList.remove('sel'));
            this.closest('.ship-opt').classList.add('sel');
            const ongkir = parseInt(this.dataset.ongkir, 10);
            document.getElementById('ongkirLabel').textContent = fmt(ongkir);
            document.getElementById('totalLabel').textContent = fmt(BASE + ongkir);
        });
    });
</script>
@endsection
