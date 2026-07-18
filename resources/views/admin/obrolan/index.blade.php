@extends('layouts.admin')

@section('title', 'Live Chat')

@section('content')
<div class="panel" style="margin-top:0;">
    <h2 style="margin-bottom:14px;">Percakapan Pelanggan</h2>
    <table>
        <thead><tr><th>Pelanggan</th><th>Status</th><th>Pesan Terakhir</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($obrolans as $o)
                <tr>
                    <td style="font-weight:700;">{{ $o->namaTampil() }}</td>
                    <td>
                        @if ($o->status === 'menunggu_admin')
                            <span class="badge b-pending">Menunggu Admin</span>
                        @elseif ($o->status === 'selesai')
                            <span class="badge b-off">Selesai</span>
                        @else
                            <span class="badge b-on">Dijawab Bot</span>
                        @endif
                    </td>
                    <td>{{ $o->terakhir_pesan_at?->diffForHumans() ?? '-' }}</td>
                    <td><a href="{{ route('admin.obrolan.show', $o) }}" class="btn btn-outline btn-sm">Buka</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty-row">Belum ada percakapan.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $obrolans->links() }}
</div>
@endsection
