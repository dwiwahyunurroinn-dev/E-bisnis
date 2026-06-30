@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <form class="search" method="get">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode pesanan...">
            <select name="status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (['pending','lunas','diproses','dikirim','selesai','batal'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline btn-sm">Cari</button>
        </form>
    </div>

    <table>
        <thead><tr><th>Kode</th><th>Tanggal</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($pesanan as $p)
                <tr>
                    <td style="font-weight:700;">{{ $p->kode }}</td>
                    <td>{{ $p->created_at->format('d M Y, H:i') }}</td>
                    <td>{{ $p->user?->name ?? '-' }}</td>
                    <td>Rp{{ number_format($p->total, 0, ',', '.') }}</td>
                    <td><span class="badge b-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td><a href="{{ route('admin.pesanan.show', $p) }}" class="btn btn-outline btn-sm">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-row">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $pesanan->links() }}
</div>
@endsection
