<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('toko.nama') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#00a868; --primary-dark:#048a55; --primary-deep:#03734a; --primary-soft:#e7f7ef; --primary-mint:#f4fbf8;
            --accent:#ff8a3d; --star:#ffb400; --danger:#e2574c; --ink:#1f2a37; --ink-soft:#66727f; --line:#eceff3; --bg:#f5f6f8; --white:#fff;
            --radius:14px; --shadow-sm:0 1px 3px rgba(16,24,40,.06); --shadow-md:0 6px 18px rgba(16,24,40,.08); --shadow-lg:0 16px 40px rgba(16,24,40,.14); --ease:cubic-bezier(.22,.61,.36,1);
            --sidebar:264px;
        }
        .ico { display:inline-block; vertical-align:middle; flex-shrink:0; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',system-ui,sans-serif; background:var(--bg); color:var(--ink); -webkit-font-smoothing:antialiased; }
        a { text-decoration:none; color:inherit; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }
        @keyframes pop { 0%{transform:scale(1);} 40%{transform:scale(1.35);} 100%{transform:scale(1);} }
        @keyframes toastIn { from{opacity:0;transform:translate(-50%,-16px);} to{opacity:1;transform:translate(-50%,0);} }

        /* Sidebar */
        .sidebar { position:fixed; top:0; left:0; width:var(--sidebar); height:100vh; background:#15281f; color:#cfe0d8; display:flex; flex-direction:column; padding:20px 0; overflow-y:auto; z-index:40; }
        .sidebar .brand { display:flex; align-items:center; gap:10px; font-size:1.2rem; font-weight:800; color:#fff; padding:6px 22px 18px; }
        .sidebar .brand .leaf { width:34px; height:34px; display:grid; place-items:center; background:linear-gradient(135deg,var(--primary),var(--primary-deep)); border-radius:10px; }
        .nav-group { font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#6f8a7e; padding:16px 22px 8px; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:11px 22px; font-size:.92rem; font-weight:600; color:#cfe0d8; transition:background .15s,color .15s; border-left:3px solid transparent; }
        .nav-item:hover { background:rgba(255,255,255,.05); color:#fff; }
        .nav-item.active { background:rgba(58,161,126,.18); color:#fff; border-left-color:var(--primary); }
        .nav-item .ic { width:22px; text-align:center; }
        .nav-item .pill { margin-left:auto; background:var(--danger); color:#fff; font-size:.68rem; font-weight:700; padding:1px 7px; border-radius:999px; }

        /* Main */
        .main { margin-left:var(--sidebar); min-height:100vh; }
        .topbar { background:var(--white); height:64px; display:flex; align-items:center; justify-content:space-between; padding:0 26px; box-shadow:var(--shadow-sm); position:sticky; top:0; z-index:30; }
        .topbar h1 { font-size:1.15rem; font-weight:800; }
        .topbar .right { display:flex; align-items:center; gap:14px; }
        .topbar .right a.view { font-size:.85rem; color:var(--primary); font-weight:700; }
        .avatar { display:flex; align-items:center; gap:9px; font-size:.88rem; font-weight:600; }
        .avatar .circ { width:36px; height:36px; border-radius:50%; background:var(--primary-soft); color:var(--primary-deep); display:grid; place-items:center; font-weight:800; }
        .content { padding:26px; animation:fadeUp .4s var(--ease) both; }

        /* Components */
        .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .stat { background:var(--white); border-radius:var(--radius); padding:18px 20px; box-shadow:var(--shadow-sm); transition:transform .2s var(--ease),box-shadow .2s; }
        .stat:hover { transform:translateY(-4px); box-shadow:var(--shadow-md); }
        .stat .lbl { font-size:.8rem; color:var(--ink-soft); display:flex; align-items:center; gap:8px; }
        .stat .val { font-size:1.6rem; font-weight:800; margin-top:8px; }
        .stat .val.sm { font-size:1.25rem; }
        .panel { background:var(--white); border-radius:var(--radius); box-shadow:var(--shadow-sm); padding:20px 22px; margin-top:18px; }
        .panel h2 { font-size:1.05rem; font-weight:800; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
        .panel h2 .btn, .panel h2 a.btn { margin-left:auto; }
        table { width:100%; border-collapse:collapse; font-size:.88rem; }
        th { text-align:left; color:var(--ink-soft); font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.4px; padding:10px 12px; border-bottom:2px solid var(--line); }
        td { padding:12px; border-bottom:1px solid var(--line); }
        tr:hover td { background:var(--primary-mint); }
        .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:.74rem; font-weight:700; }
        .b-pending{background:#fdf0d8;color:#9a6a12;} .b-lunas,.b-diproses,.b-dikirim,.b-selesai{background:var(--primary-soft);color:var(--primary-deep);} .b-batal{background:#fde3e1;color:#a3322a;}
        .b-admin{background:#e0e7ff;color:#3a44a3;} .b-pelanggan{background:var(--primary-soft);color:var(--primary-deep);}
        .b-on{background:var(--primary-soft);color:var(--primary-deep);} .b-off{background:#eee;color:#888;}

        .btn { display:inline-flex; align-items:center; gap:7px; justify-content:center; padding:10px 18px; border:none; border-radius:10px; font-size:.88rem; font-weight:700; cursor:pointer; font-family:inherit; transition:transform .15s,background .15s,box-shadow .15s; }
        .btn:active { transform:scale(.97); }
        .btn-primary { background:var(--primary); color:#fff; } .btn-primary:hover { background:var(--primary-dark); box-shadow:var(--shadow-md); }
        .btn-outline { background:#fff; color:var(--primary-dark); border:1.6px solid var(--primary); } .btn-outline:hover { background:var(--primary-soft); }
        .btn-danger { background:#fde3e1; color:var(--danger); } .btn-danger:hover { background:var(--danger); color:#fff; }
        .btn-sm { padding:6px 12px; font-size:.8rem; }

        .field { margin-bottom:15px; }
        .field label { display:block; font-size:.85rem; font-weight:600; margin-bottom:6px; }
        .field input, .field select, .field textarea { width:100%; padding:10px 13px; border:1.6px solid var(--line); border-radius:10px; font-family:inherit; font-size:.9rem; outline:none; background:#fafdfb; transition:border-color .2s,box-shadow .2s; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 4px var(--primary-soft); }
        .field .err { color:var(--danger); font-size:.78rem; margin-top:4px; }
        .two { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .toolbar { display:flex; gap:10px; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; }
        .toolbar .search { display:flex; gap:8px; }
        .toolbar .search input, .toolbar .search select { padding:9px 13px; border:1.6px solid var(--line); border-radius:10px; font-family:inherit; background:#fff; }
        .thumb-sm { width:46px; height:46px; border-radius:9px; background:linear-gradient(135deg,#eef5f1,#dcebe3); display:grid; place-items:center; overflow:hidden; }
        .thumb-sm img { width:100%; height:100%; object-fit:cover; }
        .empty-row { text-align:center; color:var(--ink-soft); padding:34px; }
        .pagination { display:flex; gap:6px; list-style:none; margin-top:16px; flex-wrap:wrap; }
        .pagination a, .pagination span { min-width:34px; height:34px; padding:0 10px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid var(--line); background:#fff; font-size:.85rem; font-weight:600; color:var(--ink-soft); }
        .pagination [aria-current] span { background:var(--primary); color:#fff; border-color:var(--primary); }

        /* Lonceng notifikasi (dipakai partials/notifikasi) */
        .icon-btn { display:inline-flex; align-items:center; gap:7px; padding:8px 12px; border-radius:10px; color:var(--ink-soft); font-weight:600; font-size:.88rem; background:none; border:none; cursor:pointer; font-family:inherit; transition:background .15s,color .15s; }
        .icon-btn:hover { background:var(--primary-soft); color:var(--primary-deep); }
        .notif { position:relative; }
        .notif-btn { position:relative; }
        .notif-dot { position:absolute; top:0; right:2px; min-width:17px; height:17px; padding:0 4px; background:var(--danger); color:#fff; font-size:.64rem; font-weight:700; border-radius:9px; display:grid; place-items:center; }
        .notif-panel { position:absolute; right:0; top:calc(100% + 8px); width:340px; max-width:90vw; background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:var(--shadow-lg); opacity:0; visibility:hidden; transform:translateY(-6px); transition:all .18s var(--ease); z-index:60; overflow:hidden; }
        .notif.open .notif-panel { opacity:1; visibility:visible; transform:translateY(0); }
        .notif-panel .nhead { display:flex; justify-content:space-between; align-items:center; padding:13px 16px; border-bottom:1px solid var(--line); font-weight:700; font-size:.9rem; }
        .notif-panel .nhead a { font-size:.78rem; color:var(--primary); font-weight:600; }
        .notif-list { max-height:360px; overflow-y:auto; }
        .notif-list a { display:flex; gap:10px; padding:12px 16px; border-bottom:1px solid var(--line); transition:background .15s; }
        .notif-list a:hover { background:var(--primary-mint); }
        .notif-list a.unread { background:#f0f9f4; }
        .notif-list .ni-ico { width:34px; height:34px; flex-shrink:0; border-radius:9px; background:var(--primary-soft); color:var(--primary-deep); display:grid; place-items:center; }
        .notif-list .ni-body b { font-size:.85rem; display:block; } .notif-list .ni-body span { font-size:.78rem; color:var(--ink-soft); } .notif-list .ni-body time { font-size:.72rem; color:var(--ink-soft); }
        .notif-empty { padding:36px 16px; text-align:center; color:var(--ink-soft); font-size:.85rem; }

        .toast { position:fixed; top:80px; left:50%; transform:translateX(-50%); z-index:100; padding:13px 22px; border-radius:12px; font-weight:600; font-size:.9rem; box-shadow:var(--shadow-md); animation:toastIn .35s var(--ease) both; display:inline-flex; align-items:center; gap:8px; }
        .toast.sukses { background:var(--primary); color:#fff; } .toast.error { background:var(--danger); color:#fff; }
        .hamb { display:none; }
        @media (max-width:980px) { .cards { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:760px) {
            .sidebar { transform:translateX(-100%); transition:transform .25s var(--ease); } .sidebar.open { transform:none; }
            .main { margin-left:0; } .hamb { display:grid; place-items:center; width:40px; height:40px; border-radius:10px; background:var(--primary-soft); border:none; cursor:pointer; font-size:1.2rem; } .two { grid-template-columns:1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php $r = request()->route()->getName(); @endphp
    <aside class="sidebar" id="sidebar">
        <div class="brand"><x-logo :size="34" light /></div>

        <div class="nav-group">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ $r==='admin.dashboard'?'active':'' }}"><span class="ic"><x-icon name="dashboard" :size="19"/></span> Dashboard</a>
        <a href="{{ route('admin.pesanan.index') }}" class="nav-item {{ str_starts_with($r,'admin.pesanan')?'active':'' }}">
            <span class="ic"><x-icon name="clipboard" :size="19"/></span> Pesanan
            @php $pending = \App\Models\Pesanan::where('status','pending')->count(); @endphp
            @if ($pending) <span class="pill">{{ $pending }}</span> @endif
        </a>
        <a href="{{ route('admin.laporan.index') }}" class="nav-item {{ str_starts_with($r,'admin.laporan')?'active':'' }}"><span class="ic"><x-icon name="chart" :size="19"/></span> Laporan</a>

        <div class="nav-group">Katalog</div>
        <a href="{{ route('admin.produk.index') }}" class="nav-item {{ str_starts_with($r,'admin.produk')?'active':'' }}"><span class="ic"><x-icon name="package" :size="19"/></span> Produk</a>
        <a href="{{ route('admin.stok.index') }}" class="nav-item {{ str_starts_with($r,'admin.stok')?'active':'' }}"><span class="ic"><x-icon name="box" :size="19"/></span> Manajemen Stok</a>
        <a href="{{ route('admin.promo.index') }}" class="nav-item {{ str_starts_with($r,'admin.promo')?'active':'' }}"><span class="ic"><x-icon name="tag" :size="19"/></span> Promo / Slider</a>
        <a href="{{ route('admin.voucher.index') }}" class="nav-item {{ str_starts_with($r,'admin.voucher')?'active':'' }}"><span class="ic"><x-icon name="tag" :size="19"/></span> Voucher</a>
        <a href="{{ route('admin.bundle.index') }}" class="nav-item {{ str_starts_with($r,'admin.bundle')?'active':'' }}"><span class="ic"><x-icon name="package" :size="19"/></span> Bundle / Paket</a>

        <div class="nav-group">Chatbot CRM</div>
        <a href="{{ route('admin.obrolan.index') }}" class="nav-item {{ str_starts_with($r,'admin.obrolan')?'active':'' }}">
            <span class="ic"><x-icon name="chat" :size="19"/></span> Live Chat
            @php $menungguAdmin = \App\Models\Obrolan::menungguAdmin()->count(); @endphp
            @if ($menungguAdmin) <span class="pill">{{ $menungguAdmin }}</span> @endif
        </a>
        <a href="{{ route('admin.faq.index') }}" class="nav-item {{ str_starts_with($r,'admin.faq')?'active':'' }}"><span class="ic"><x-icon name="clipboard" :size="19"/></span> FAQ Chatbot</a>

        <div class="nav-group">Pengguna</div>
        <a href="{{ route('admin.pelanggan.index') }}" class="nav-item {{ str_starts_with($r,'admin.pelanggan')?'active':'' }}"><span class="ic"><x-icon name="users" :size="19"/></span> Pelanggan</a>
        <a href="{{ route('admin.user.index') }}" class="nav-item {{ str_starts_with($r,'admin.user')?'active':'' }}"><span class="ic"><x-icon name="key" :size="19"/></span> Manajemen User</a>
        <a href="{{ route('admin.log.index') }}" class="nav-item {{ str_starts_with($r,'admin.log')?'active':'' }}"><span class="ic"><x-icon name="clock" :size="19"/></span> Log Aktivitas</a>

        <div class="nav-group">Lainnya</div>
        <a href="{{ route('admin.pengaturan.index') }}" class="nav-item {{ str_starts_with($r,'admin.pengaturan')?'active':'' }}"><span class="ic"><x-icon name="settings" :size="19"/></span> Pengaturan</a>
        <a href="{{ route('produk.index') }}" class="nav-item"><span class="ic"><x-icon name="store" :size="19"/></span> Lihat Toko</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-item" type="submit" style="width:100%;border:none;background:none;cursor:pointer;font-family:inherit;"><span class="ic"><x-icon name="logout" :size="19"/></span> Keluar</button></form>
    </aside>

    <div class="main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="hamb" onclick="document.getElementById('sidebar').classList.toggle('open')"><x-icon name="menu"/></button>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <div class="right">
                <a href="{{ route('produk.index') }}" class="view" target="_blank" style="display:inline-flex;align-items:center;gap:5px;"><x-icon name="store" :size="16"/> Buka Toko</a>
                @include('partials.notifikasi')
                <div class="avatar"><span class="circ">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span> {{ auth()->user()->name }}</div>
            </div>
        </div>

        @if (session('sukses'))<div class="toast sukses" data-toast><x-icon name="check-circle" :size="18"/> {{ session('sukses') }}</div>@elseif (session('error'))<div class="toast error" data-toast><x-icon name="alert" :size="18"/> {{ session('error') }}</div>@endif

        <div class="content">@yield('content')</div>
    </div>

    <script>
        document.querySelectorAll('[data-toast]').forEach(t => {
            setTimeout(() => { t.style.transition='opacity .4s,transform .4s'; t.style.opacity='0'; t.style.transform='translate(-50%,-16px)'; }, 2600);
            setTimeout(() => t.remove(), 3100);
        });
        function konfirmHapus(e) { if (!confirm('Yakin ingin menghapus data ini?')) e.preventDefault(); }
        document.addEventListener('click', (e) => {
            document.querySelectorAll('.notif.open').forEach(el => { if (!el.contains(e.target)) el.classList.remove('open'); });
        });
    </script>
    @stack('scripts')
</body>
</html>
