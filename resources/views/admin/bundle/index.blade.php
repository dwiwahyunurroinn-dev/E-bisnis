@extends('layouts.admin')

@section('title', 'Bundle / Paket')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;">Paket Bundle</h2>
        <a href="{{ route('admin.bundle.create') }}" class="btn btn-primary">+ Buat Bundle</a>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Isi Paket</th><th>Harga Bundle</th><th>Harga Normal</th><th>Hemat</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($bundles as $b)
                <tr>
                    <td style="font-weight:600;">{{ $b->nama }}</td>
                    <td style="font-size:.82rem;color:var(--ink-soft);">{{ $b->produk->pluck('nama')->join(', ') }}</td>
                    <td>Rp{{ number_format($b->harga_bundle,0,',','.') }}</td>
                    <td>Rp{{ number_format($b->hargaNormal(),0,',','.') }}</td>
                    <td style="color:var(--primary-deep);font-weight:700;">Rp{{ number_format($b->hemat(),0,',','.') }}</td>
                    <td><span class="badge {{ $b->aktif ? 'b-on' : 'b-off' }}">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.bundle.edit', $b) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.bundle.destroy', $b) }}" onsubmit="konfirmHapus(event)">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Belum ada bundle.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
