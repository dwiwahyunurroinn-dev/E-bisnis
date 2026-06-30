@extends('layouts.app')

@section('title', 'Notifikasi — '.config('toko.nama'))

@push('styles')
<style>
    .notif-wrap { max-width: 720px; margin: 28px auto 60px; }
    .nrow { display: flex; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--line); }
    .nrow:last-child { border-bottom: none; }
    .nrow.unread { background: var(--primary-mint); margin: 0 -16px; padding: 14px 16px; border-radius: 10px; border-bottom: none; }
    .nrow .ni-ico { width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px; background: var(--primary-soft); color: var(--primary-deep); display: grid; place-items: center; }
    .nrow .b { font-weight: 700; font-size: .92rem; }
    .nrow .p { font-size: .85rem; color: var(--ink-soft); }
    .nrow time { font-size: .75rem; color: var(--ink-soft); }
</style>
@endpush

@section('content')
<div class="container notif-wrap">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="font-size:1.3rem;font-weight:800;">Notifikasi</h2>
        @if ($notifikasi->isNotEmpty())
            <form method="POST" action="{{ route('notifikasi.baca-semua') }}">@csrf
                <button class="btn btn-outline" style="padding:9px 16px;"><x-icon name="check" :size="16"/> Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    <div class="card-panel">
        @forelse ($notifikasi as $n)
            <a href="{{ route('notifikasi.buka', $n) }}" class="nrow {{ $n->belumDibaca() ? 'unread' : '' }}">
                <span class="ni-ico"><x-icon name="{{ $n->tipe === 'pesanan' ? 'cart' : ($n->tipe === 'status' ? 'truck' : 'bell') }}" :size="20"/></span>
                <div style="flex:1;">
                    <div class="b">{{ $n->judul }}</div>
                    @if ($n->pesan)<div class="p">{{ $n->pesan }}</div>@endif
                    <time>{{ $n->created_at->diffForHumans() }}</time>
                </div>
            </a>
        @empty
            <div class="empty"><x-mascot :size="130" /><p style="margin-top:8px;">Belum ada notifikasi.</p></div>
        @endforelse
        <div style="margin-top:14px;">{{ $notifikasi->links() }}</div>
    </div>
</div>
@endsection
