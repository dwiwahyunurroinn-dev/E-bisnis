@extends('layouts.admin')

@section('title', 'Pesanan '.$pesanan->kode)

@section('content')
<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:18px;">
    <div>
        <div class="panel" style="margin-top:0;">
            <h2> {{ $pesanan->kode }} <span class="badge b-{{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span></h2>
            <table>
                <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
                <tbody>
                    @foreach ($pesanan->detail as $d)
                        <tr><td>{{ $d->nama_produk }}</td><td>Rp{{ number_format($d->harga,0,',','.') }}</td><td>{{ $d->jumlah }}</td><td>Rp{{ number_format($d->subtotal,0,',','.') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:14px;font-size:.9rem;">
                <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--ink-soft)">Subtotal</span><span>Rp{{ number_format($pesanan->subtotal,0,',','.') }}</span></div>
                <div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--ink-soft)">Ongkir ({{ strtoupper($pesanan->kurir) }} {{ $pesanan->layanan }})</span><span>Rp{{ number_format($pesanan->ongkir,0,',','.') }}</span></div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:1.5px dashed var(--line);font-weight:800;font-size:1.15rem;"><span>Total</span><span style="color:var(--primary-deep)">Rp{{ number_format($pesanan->total,0,',','.') }}</span></div>
            </div>
        </div>
    </div>

    <div>
        <div class="panel" style="margin-top:0;">
            <h2>Pelanggan</h2>
            <p style="font-size:.9rem;"><b>{{ $pesanan->user?->name }}</b><br>{{ $pesanan->user?->email }}<br>{{ $pesanan->telepon }}</p>
            @if ($pesanan->labelMetode())
                <h2 style="margin-top:18px;">Pembayaran</h2>
                <p style="font-size:.9rem;">{{ $pesanan->labelMetode() }}
                    @if ($pesanan->isCod())<br><b style="color:var(--danger);">Tagih tunai Rp{{ number_format($pesanan->total, 0, ',', '.') }} saat pengiriman</b>@endif
                    @if ($pesanan->nomorVa())<br>VA: {{ $pesanan->nomorVa() }}@endif
                </p>
            @endif
            <h2 style="margin-top:18px;">Alamat</h2>
            <p style="font-size:.9rem;">{{ $pesanan->penerima }}<br>{{ $pesanan->alamat_lengkap }}, {{ $pesanan->kota }} {{ $pesanan->kode_pos }}</p>
        </div>

        <div class="panel">
            <h2> Ubah Status</h2>
            <form method="POST" action="{{ route('admin.pesanan.update', $pesanan) }}">
                @csrf @method('PATCH')
                <div class="field">
                    <select name="status">
                        @foreach (['pending','lunas','diproses','dikirim','selesai','batal'] as $s)
                            <option value="{{ $s }}" {{ $pesanan->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>No. Resi (isi saat status Dikirim)</label>
                    <input type="text" name="resi" value="{{ old('resi', $pesanan->resi) }}" placeholder="mis. JNE1234567890">
                    @error('resi')<div class="err">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary btn-block" style="width:100%;">Simpan Status</button>
            </form>
            <p style="font-size:.76rem;color:var(--ink-soft);margin-top:8px;">Mengubah ke <b>Lunas</b> dari pending akan mengurangi stok. Resi akan tampil di halaman pesanan pelanggan.</p>
        </div>
    </div>
</div>
<a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline" style="margin-top:18px;">← Kembali</a>
@endsection
