@extends('layouts.admin')

@section('title', 'Voucher')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;">Daftar Voucher</h2>
        <a href="{{ route('admin.voucher.create') }}" class="btn btn-primary">+ Buat Voucher</a>
    </div>
    <table>
        <thead><tr><th>Kode</th><th>Tipe</th><th>Nilai</th><th>Min. Belanja</th><th>Kuota</th><th>Berakhir</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($vouchers as $v)
                <tr>
                    <td style="font-weight:700;">{{ $v->kode }}</td>
                    <td>{{ ucfirst($v->tipe) }}</td>
                    <td>{{ $v->tipe === 'persen' ? rtrim(rtrim(number_format($v->nilai,2,',','.'),'0'),',').'%' : 'Rp'.number_format($v->nilai,0,',','.') }}</td>
                    <td>Rp{{ number_format($v->min_belanja,0,',','.') }}</td>
                    <td>{{ $v->kuota ? $v->terpakai.'/'.$v->kuota : '∞' }}</td>
                    <td>{{ $v->kadaluarsa?->format('d M Y') ?? '-' }}</td>
                    <td><span class="badge {{ $v->aktif ? 'b-on' : 'b-off' }}">{{ $v->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.voucher.edit', $v) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.voucher.destroy', $v) }}" onsubmit="konfirmHapus(event)">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty-row">Belum ada voucher.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $vouchers->links() }}
</div>
@endsection
