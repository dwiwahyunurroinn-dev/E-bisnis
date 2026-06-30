@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;">📜 Log Aktivitas Sistem</h2>
        <form class="search" method="get">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aktivitas...">
            <button class="btn btn-outline btn-sm">Cari</button>
        </form>
    </div>
    <table>
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Subjek</th><th>Keterangan</th><th>IP</th></tr></thead>
        <tbody>
            @forelse ($log as $l)
                <tr>
                    <td style="white-space:nowrap;">{{ $l->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $l->user?->name ?? 'Sistem' }}</td>
                    <td><span class="badge b-pelanggan">{{ $l->aksi }}</span></td>
                    <td>{{ $l->subjek ?? '-' }}</td>
                    <td>{{ $l->deskripsi ?? '-' }}</td>
                    <td style="color:var(--ink-soft);font-size:.8rem;">{{ $l->ip ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-row">Belum ada aktivitas tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $log->links() }}
</div>
@endsection
