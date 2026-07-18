@extends('layouts.admin')

@section('title', 'FAQ Chatbot')

@section('content')
<div class="panel" style="margin-top:0;">
    <div class="toolbar">
        <h2 style="margin:0;">Daftar FAQ</h2>
        <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">+ Tambah FAQ</a>
    </div>
    <p style="color:var(--ink-soft);font-size:.85rem;margin:-6px 0 16px;">
        Chatbot mencari kata kunci di pesan pelanggan lalu membalas otomatis dengan jawaban di bawah.
        Jika tidak ada yang cocok, percakapan diteruskan ke menu <b>Live Chat</b>.
    </p>
    <table>
        <thead><tr><th>Pertanyaan</th><th>Kata Kunci</th><th>Kategori</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse ($faqs as $f)
                <tr>
                    <td style="font-weight:700;max-width:260px;">{{ $f->pertanyaan }}</td>
                    <td style="color:var(--ink-soft);">{{ $f->kata_kunci }}</td>
                    <td>{{ $f->kategori ?: '-' }}</td>
                    <td>{{ $f->urutan }}</td>
                    <td><span class="badge {{ $f->aktif ? 'b-on' : 'b-off' }}">{{ $f->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('admin.faq.edit', $f) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.faq.destroy', $f) }}" onsubmit="konfirmHapus(event)">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-row">Belum ada FAQ.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
