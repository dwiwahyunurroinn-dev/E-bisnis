@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <form class="search" method="get">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk...">
            <button class="btn btn-outline btn-sm">Cari</button>
        </form>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    </div>

    <table>
        <thead><tr><th></th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($produk as $p)
                <tr>
                    <td><div class="thumb-sm">@if ($p->gambarUrl())<img src="{{ $p->gambarUrl() }}" alt="">@else <span style="color:var(--primary)"><x-icon name="sofa" :size="22"/></span>@endif</div></td>
                    <td style="font-weight:600;">{{ $p->nama }}</td>
                    <td>{{ $p->kategori->nama }}</td>
                    <td>Rp{{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td><span class="badge {{ $p->stok == 0 ? 'b-batal' : ($p->stok < 5 ? 'b-pending' : 'b-on') }}">{{ $p->stok }}</span></td>
                    <td><span class="badge {{ $p->status === 'aktif' ? 'b-on' : 'b-off' }}">{{ ucfirst($p->status) }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.produk.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.produk.destroy', $p) }}" onsubmit="konfirmHapus(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $produk->links() }}
</div>
@endsection
