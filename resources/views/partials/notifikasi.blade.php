<div class="notif">
    <button class="icon-btn notif-btn" type="button" onclick="this.closest('.notif').classList.toggle('open')" aria-label="Notifikasi">
        <x-icon name="bell"/>
        @if (($notifBelum ?? 0) > 0)<span class="notif-dot">{{ $notifBelum > 9 ? '9+' : $notifBelum }}</span>@endif
    </button>
    <div class="notif-panel">
        <div class="nhead">
            <span>Notifikasi</span>
            @if (($notifBelum ?? 0) > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">@csrf<button type="submit" style="background:none;border:none;cursor:pointer;color:var(--primary);font-weight:600;font-family:inherit;">Tandai semua dibaca</button></form>
            @endif
        </div>
        <div class="notif-list">
            @forelse (($notifList ?? collect()) as $n)
                <a href="{{ route('notifikasi.buka', $n) }}" class="{{ $n->belumDibaca() ? 'unread' : '' }}">
                    <span class="ni-ico"><x-icon name="{{ $n->tipe === 'pesanan' ? 'cart' : ($n->tipe === 'status' ? 'truck' : ($n->tipe === 'stok' ? 'alert' : 'bell')) }}" :size="18"/></span>
                    <span class="ni-body">
                        <b>{{ $n->judul }}</b>
                        @if ($n->pesan)<span>{{ \Illuminate\Support\Str::limit($n->pesan, 60) }}</span>@endif
                        <time>{{ $n->created_at->diffForHumans() }}</time>
                    </span>
                </a>
            @empty
                <div class="notif-empty">Belum ada notifikasi.</div>
            @endforelse
        </div>
        <div class="nhead" style="border-top:1px solid var(--line);border-bottom:none;justify-content:center;">
            <a href="{{ route('notifikasi.index') }}">Lihat semua notifikasi</a>
        </div>
    </div>
</div>
