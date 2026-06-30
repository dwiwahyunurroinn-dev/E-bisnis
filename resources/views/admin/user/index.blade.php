@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;"> Pengguna Sistem</h2>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">+ Tambah User</a>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($users as $u)
                <tr>
                    <td style="font-weight:600;">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge b-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                    <td><span class="badge {{ $u->aktif ? 'b-on' : 'b-off' }}">{{ $u->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.user.edit', $u) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.user.destroy', $u) }}" onsubmit="konfirmHapus(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-row">Belum ada user.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
