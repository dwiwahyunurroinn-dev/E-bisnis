@extends('layouts.admin')

@section('title', 'Pelanggan: '.$pelanggan->name)

@section('content')
<div class="panel" style="margin-top:0;max-width:560px;">
    <h2> {{ $pelanggan->name }}</h2>
    <p style="font-size:.9rem;line-height:1.9;">
        <b>Email:</b> {{ $pelanggan->email }}<br>
        <b>Telepon:</b> {{ $pelanggan->telepon ?? '-' }}<br>
        <b>Bergabung:</b> {{ $pelanggan->created_at->format('d M Y') }}<br>
        <b>Status:</b> <span class="badge {{ $pelanggan->aktif ? 'b-on' : 'b-off' }}">{{ $pelanggan->aktif ? 'Aktif' : 'Nonaktif' }}</span>
    </p>
</div>

<div class="panel">
    <h2> Riwayat Pesanan ({{ $pelanggan->pesanan->count() }})</h2>
    <table>
        <thead><tr><th>Kode</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
            @forelse ($pelanggan->pesanan as $p)
                <tr>
                    <td style="font-weight:700;">{{ $p->kode }}</td>
                    <td>{{ $p->created_at->format('d M Y') }}</td>
                    <td>Rp{{ number_format($p->total,0,',','.') }}</td>
                    <td><span class="badge b-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td><a href="{{ route('admin.pesanan.show', $p) }}" class="btn btn-outline btn-sm">Lihat</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-row">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<a href="{{ route('admin.pelanggan.index') }}" class="btn btn-outline" style="margin-top:18px;">← Kembali</a>
@endsection
