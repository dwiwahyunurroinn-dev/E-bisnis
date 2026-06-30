@extends('layouts.admin')

@section('title', 'Manajemen Stok')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;"> Stok Produk</h2>
        <form class="search" method="get">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk...">
            <button class="btn btn-outline btn-sm">Cari</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Produk</th><th>Kategori</th><th>Stok Saat Ini</th><th>Ubah Stok</th></tr></thead>
        <tbody>
            @forelse ($produk as $p)
                <tr>
                    <td style="font-weight:600;">{{ $p->nama }}</td>
                    <td>{{ $p->kategori->nama }}</td>
                    <td><span class="badge {{ $p->stok == 0 ? 'b-batal' : ($p->stok < 5 ? 'b-pending' : 'b-on') }}">{{ $p->stok }} unit</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.stok.produk', $p) }}" style="display:flex;gap:8px;">
                            @csrf @method('PATCH')
                            <input type="number" name="stok" value="{{ $p->stok }}" min="0" style="width:90px;padding:7px 10px;border:1.6px solid var(--line);border-radius:8px;">
                            <button class="btn btn-primary btn-sm">Simpan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-row">Tidak ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $produk->links() }}
</div>

<div class="panel">
    <h2> Stok Bahan Baku (Supply Chain)</h2>
    <table>
        <thead><tr><th>Bahan Baku</th><th>Satuan</th><th>Stok</th><th>Ubah</th></tr></thead>
        <tbody>
            @foreach ($bahanBaku as $b)
                <tr>
                    <td style="font-weight:600;">{{ $b->nama }}</td>
                    <td>{{ $b->satuan }}</td>
                    <td>{{ rtrim(rtrim(number_format($b->stok,2,',','.'),'0'),',') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.stok.bahan', $b) }}" style="display:flex;gap:8px;">
                            @csrf @method('PATCH')
                            <input type="number" step="0.01" name="stok" value="{{ $b->stok }}" min="0" style="width:100px;padding:7px 10px;border:1.6px solid var(--line);border-radius:8px;">
                            <button class="btn btn-primary btn-sm">Simpan</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
