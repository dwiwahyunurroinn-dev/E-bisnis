@extends('layouts.admin')

@section('title', 'Promo / Slider')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;">🎉 Slide Promo Beranda</h2>
        <a href="{{ route('admin.promo.create') }}" class="btn btn-primary">＋ Tambah Promo</a>
    </div>
    <table>
        <thead><tr><th>Urutan</th><th>Preview</th><th>Judul</th><th>Label</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($promos as $p)
                <tr>
                    <td>{{ $p->urutan }}</td>
                    <td><div class="thumb-sm" style="width:70px;background:{{ $p->warna }};">@if($p->gambar)<img src="{{ asset('storage/'.$p->gambar) }}" alt="">@endif</div></td>
                    <td style="font-weight:600;">{{ $p->judul }}</td>
                    <td>{{ $p->label ?? '-' }}</td>
                    <td><span class="badge {{ $p->aktif ? 'b-on' : 'b-off' }}">{{ $p->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.promo.edit', $p) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.promo.destroy', $p) }}" onsubmit="konfirmHapus(event)">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-row">Belum ada promo.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
