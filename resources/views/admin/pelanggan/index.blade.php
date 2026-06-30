@extends('layouts.admin')

@section('title', 'Pelanggan')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;"> Daftar Pelanggan</h2>
        <form class="search" method="get">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email...">
            <button class="btn btn-outline btn-sm">Cari</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Pesanan</th><th>Total Belanja</th><th>Bergabung</th><th></th></tr></thead>
        <tbody>
            @forelse ($pelanggan as $u)
                <tr>
                    <td style="font-weight:600;">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->telepon ?? '-' }}</td>
                    <td>{{ $u->pesanan_count }}</td>
                    <td>Rp{{ number_format($u->total_belanja ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $u->created_at->format('d M Y') }}</td>
                    <td><a href="{{ route('admin.pelanggan.show', $u) }}" class="btn btn-outline btn-sm">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">Belum ada pelanggan.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $pelanggan->links() }}
</div>
@endsection
