<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('toko.nama').' — '.config('toko.tagline'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      #3aa17e;   /* hijau soft */
            --primary-dark: #2c8064;
            --primary-deep: #1f6b52;
            --primary-soft: #eaf6f1;
            --primary-mint: #f4faf7;
            --accent:       #e8a951;
            --danger:       #e2574c;
            --ink:          #213a32;
            --ink-soft:     #6a7d75;
            --line:         #e9efeb;
            --bg:           #f3f8f5;
            --white:        #ffffff;
            --radius:       16px;
            --shadow-sm:    0 2px 8px rgba(31,107,82,.06);
            --shadow-md:    0 8px 24px rgba(31,107,82,.10);
            --shadow-lg:    0 18px 48px rgba(31,107,82,.16);
            --ease:         cubic-bezier(.22,.61,.36,1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg); color: var(--ink); line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        .container { max-width: 1240px; margin: 0 auto; padding: 0 20px; }

        /* ============ ANIMASI ============ */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float  { 0%,100% { transform: translateY(0) rotate(-12deg); } 50% { transform: translateY(-18px) rotate(-8deg); } }
        @keyframes shimmer { 0% { background-position: -400px 0; } 100% { background-position: 400px 0; } }
        @keyframes gradientMove { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes pop { 0% { transform: scale(1); } 40% { transform: scale(1.4); } 100% { transform: scale(1); } }
        @keyframes toastIn { from { opacity: 0; transform: translate(-50%, -16px); } to { opacity: 1; transform: translate(-50%, 0); } }

        /* Reveal on scroll */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s var(--ease), transform .6s var(--ease); }
        .reveal.in { opacity: 1; transform: translateY(0); }

        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            *, *::before, *::after { animation-duration: .001ms !important; }
        }

        /* ============ TOP BAR ============ */
        .topbar { background: var(--primary-deep); color: #cfeadd; font-size: .8rem; }
        .topbar .container { display: flex; justify-content: space-between; align-items: center; height: 34px; }
        .topbar a { opacity: .9; transition: opacity .15s; }
        .topbar a:hover { opacity: 1; }

        /* ============ HEADER ============ */
        header { background: rgba(255,255,255,.92); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 50; box-shadow: var(--shadow-sm); }
        .header-main { display: flex; align-items: center; gap: 24px; height: 72px; }
        .logo { display: flex; align-items: center; gap: 9px; font-size: 1.35rem; font-weight: 800; color: var(--primary-dark); white-space: nowrap; }
        .logo .leaf { width: 36px; height: 36px; display: grid; place-items: center; background: linear-gradient(135deg, var(--primary), var(--primary-deep)); color: #fff; border-radius: 11px; font-size: 1.1rem; box-shadow: var(--shadow-sm); transition: transform .3s var(--ease); }
        .logo:hover .leaf { transform: rotate(-10deg) scale(1.08); }
        .logo b { color: var(--ink); }

        .searchbar { flex: 1; max-width: 640px; position: relative; }
        .searchbar form { display: flex; }
        .searchbar input {
            width: 100%; height: 46px; padding: 0 52px 0 18px; font-size: .95rem; font-family: inherit;
            border: 1.8px solid var(--line); border-radius: 13px; outline: none; background: #fafdfb;
            transition: border-color .2s, box-shadow .2s;
        }
        .searchbar input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px var(--primary-soft); }
        .searchbar button {
            position: absolute; right: 6px; top: 6px; height: 34px; width: 40px; border: none; border-radius: 10px;
            background: var(--primary); color: #fff; cursor: pointer; display: grid; place-items: center; transition: background .15s, transform .15s;
        }
        .searchbar button:hover { background: var(--primary-dark); transform: scale(1.05); }

        .header-actions { display: flex; align-items: center; gap: 8px; }
        .icon-btn { display: flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 11px; color: var(--ink-soft); font-weight: 600; font-size: .9rem; transition: background .15s, color .15s, transform .15s; }
        .icon-btn:hover { background: var(--primary-soft); color: var(--primary-dark); transform: translateY(-1px); }
        .icon-btn.solid { background: var(--primary); color: #fff; }
        .icon-btn.solid:hover { background: var(--primary-dark); color: #fff; }
        .cart-wrap { position: relative; }
        .cart-badge { position: absolute; top: 0; right: 4px; background: var(--danger); color: #fff; font-size: .65rem; font-weight: 700; min-width: 18px; height: 18px; border-radius: 9px; display: grid; place-items: center; padding: 0 4px; }
        .cart-badge.pop { animation: pop .4s var(--ease); }

        /* ============ CATEGORY NAV ============ */
        .catnav { background: var(--white); border-top: 1px solid var(--line); position: sticky; top: 72px; z-index: 40; }
        .catnav .container { display: flex; gap: 8px; overflow-x: auto; padding: 10px 20px; scrollbar-width: none; }
        .catnav .container::-webkit-scrollbar { display: none; }
        .cat-pill { white-space: nowrap; padding: 8px 16px; border-radius: 999px; font-size: .88rem; font-weight: 600; color: var(--ink-soft); background: var(--bg); transition: all .18s var(--ease); }
        .cat-pill:hover { color: var(--primary-dark); background: var(--primary-soft); transform: translateY(-1px); }
        .cat-pill.active { background: var(--primary); color: #fff; box-shadow: var(--shadow-sm); }

        /* ============ HERO ============ */
        .hero { padding: 26px 0 6px; }
        .hero-banner {
            border-radius: 24px; overflow: hidden; position: relative; color: #fff; padding: 50px 52px; box-shadow: var(--shadow-md);
            background: linear-gradient(120deg, #3aa17e, #2c8064, #1f6b52, #2c8064);
            background-size: 300% 300%; animation: gradientMove 12s ease infinite;
        }
        .hero-banner::after { content: "🌿"; position: absolute; right: 50px; bottom: 0; font-size: 11rem; opacity: .15; animation: float 6s ease-in-out infinite; }
        .hero-banner h1 { font-size: 2.2rem; font-weight: 800; line-height: 1.18; max-width: 580px; animation: fadeUp .7s var(--ease) both; }
        .hero-banner p { margin-top: 12px; opacity: .92; max-width: 480px; animation: fadeUp .7s .12s var(--ease) both; }
        .hero-cta { display: inline-flex; align-items: center; gap: 8px; margin-top: 22px; background: #fff; color: var(--primary-deep); font-weight: 700; padding: 13px 28px; border-radius: 12px; animation: fadeUp .7s .24s var(--ease) both; transition: transform .2s var(--ease), box-shadow .2s; }
        .hero-cta:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        /* ============ TRUST ============ */
        .trust { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 22px 0 8px; }
        .trust-item { background: var(--white); border-radius: var(--radius); padding: 16px 18px; display: flex; gap: 12px; align-items: center; box-shadow: var(--shadow-sm); transition: transform .2s var(--ease), box-shadow .2s; }
        .trust-item:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .trust-item .t-ico { font-size: 1.6rem; }
        .trust-item b { display: block; font-size: .9rem; }
        .trust-item span { font-size: .78rem; color: var(--ink-soft); }

        /* ============ SECTION ============ */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin: 32px 0 16px; }
        .section-head h2 { font-size: 1.35rem; font-weight: 800; }
        .section-head h2::before { content: ""; display: inline-block; width: 5px; height: 22px; background: var(--primary); border-radius: 3px; margin-right: 10px; vertical-align: -4px; }
        .section-head .sub { font-size: .85rem; color: var(--ink-soft); }

        /* ============ PRODUCT GRID ============ */
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); margin-bottom: 36px; }
        .pcard { background: var(--white); border-radius: var(--radius); overflow: hidden; position: relative; box-shadow: var(--shadow-sm); border: 1px solid var(--line); transition: transform .22s var(--ease), box-shadow .22s var(--ease), border-color .22s; display: flex; flex-direction: column; }
        .pcard:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: transparent; }
        .pthumb { position: relative; aspect-ratio: 1/1; background: linear-gradient(135deg,#eef5f1,#dcebe3); display: grid; place-items: center; overflow: hidden; }
        .pthumb .ph-emoji { font-size: 3.4rem; opacity: .55; transition: transform .35s var(--ease); }
        .pcard:hover .pthumb .ph-emoji { transform: scale(1.12) rotate(-4deg); }
        .pthumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .4s var(--ease); }
        .pcard:hover .pthumb img { transform: scale(1.06); }
        .badge-disc { position: absolute; top: 10px; left: 10px; background: var(--danger); color: #fff; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 7px; z-index: 2; }
        .badge-soldout { position: absolute; inset: 0; background: rgba(255,255,255,.74); display: grid; place-items: center; z-index: 3; font-weight: 700; color: var(--ink-soft); }
        .wish { position: absolute; top: 8px; right: 8px; width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.94); display: grid; place-items: center; z-index: 2; color: var(--ink-soft); box-shadow: var(--shadow-sm); transition: color .15s, transform .15s; font-size: 1.05rem; }
        .wish:hover { color: var(--danger); transform: scale(1.15); }
        .pbody { padding: 12px 13px 15px; display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .pcat { font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; color: var(--primary); font-weight: 700; }
        .pname { font-size: .9rem; font-weight: 600; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5em; }
        .pprice { font-size: 1.08rem; font-weight: 800; margin-top: 2px; }
        .pprice-old { font-size: .76rem; color: var(--ink-soft); text-decoration: line-through; }
        .pmeta { display: flex; align-items: center; gap: 6px; font-size: .76rem; color: var(--ink-soft); margin-top: auto; padding-top: 6px; }
        .stars { color: var(--accent); font-weight: 700; }

        /* ============ BUTTONS ============ */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 13px 22px; border: none; border-radius: 12px; font-size: .96rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: transform .15s var(--ease), background .15s, box-shadow .15s; }
        .btn:active { transform: scale(.97); }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .btn-outline { background: #fff; color: var(--primary-dark); border: 1.8px solid var(--primary); }
        .btn-outline:hover { background: var(--primary-soft); }
        .btn-block { width: 100%; }
        .btn:disabled { background: #cdd8d3 !important; color: #fff !important; cursor: not-allowed; border: none; transform: none; box-shadow: none; }

        /* ============ PAGINATION ============ */
        .pagination-wrap { margin: 8px 0 50px; }
        .pagination { display: flex; gap: 6px; flex-wrap: wrap; list-style: none; justify-content: center; }
        .pagination a, .pagination span { min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: var(--white); border: 1px solid var(--line); font-size: .88rem; font-weight: 600; color: var(--ink-soft); transition: all .15s; }
        .pagination a:hover { border-color: var(--primary); color: var(--primary); }
        .pagination [aria-current] span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ============ FORM ============ */
        .field { margin-bottom: 14px; }
        .field label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 6px; }
        .field input, .field textarea { width: 100%; padding: 11px 14px; border: 1.6px solid var(--line); border-radius: 11px; font-family: inherit; font-size: .92rem; outline: none; background: #fafdfb; transition: border-color .2s, box-shadow .2s; }
        .field input:focus, .field textarea:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px var(--primary-soft); }
        .field .err { color: var(--danger); font-size: .78rem; margin-top: 4px; }
        .card-panel { background: var(--white); border-radius: var(--radius); padding: 22px 24px; box-shadow: var(--shadow-sm); }
        .card-panel h3 { font-size: 1.05rem; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

        /* ============ QTY ============ */
        .qty { display: inline-flex; align-items: center; border: 1.5px solid var(--line); border-radius: 10px; overflow: hidden; }
        .qty button { width: 36px; height: 36px; border: none; background: var(--bg); font-size: 1.2rem; cursor: pointer; color: var(--ink); transition: background .15s; }
        .qty button:hover { background: var(--primary-soft); color: var(--primary-dark); }
        .qty input { width: 46px; height: 36px; text-align: center; border: none; font-size: 1rem; font-family: inherit; font-weight: 600; background: #fff; }

        /* ============ TOAST ============ */
        .toast { position: fixed; top: 90px; left: 50%; transform: translateX(-50%); z-index: 100; padding: 13px 22px; border-radius: 12px; font-weight: 600; font-size: .9rem; box-shadow: var(--shadow-lg); animation: toastIn .35s var(--ease) both; }
        .toast.sukses { background: var(--primary); color: #fff; }
        .toast.error { background: var(--danger); color: #fff; }

        /* ============ FOOTER ============ */
        footer { background: #14271f; color: #b6c8c0; margin-top: 30px; }
        .foot-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 30px; padding: 44px 0 30px; }
        .foot-grid h4 { color: #fff; font-size: .95rem; margin-bottom: 14px; }
        .foot-grid a, .foot-grid p { display: block; font-size: .85rem; margin-bottom: 9px; color: #b6c8c0; transition: color .15s; }
        .foot-grid a:hover { color: #fff; }
        .foot-bottom { border-top: 1px solid rgba(255,255,255,.1); padding: 18px 0; text-align: center; font-size: .8rem; color: #8fa39b; }

        .empty { text-align: center; padding: 70px 0; color: var(--ink-soft); }
        .empty .big { font-size: 3.4rem; margin-bottom: 12px; }

        @media (max-width: 900px) { .trust { grid-template-columns: repeat(2, 1fr); } .foot-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { .header-actions .label { display: none; } .hero-banner { padding: 32px 24px; } .hero-banner h1 { font-size: 1.55rem; } }
    </style>
    @stack('styles')
</head>
<body>
    <div class="topbar">
        <div class="container">
            <span>♻️ Furnitur dari 100% kayu daur ulang — ramah lingkungan</span>
            <span>
                <a href="https://instagram.com/{{ config('toko.instagram') }}" target="_blank" rel="noopener">📷 @{{ config('toko.instagram') }}</a>
                &nbsp;·&nbsp; <a href="#">Bantuan</a> &nbsp;·&nbsp; <a href="#">Lacak Pesanan</a>
            </span>
        </div>
    </div>

    <header>
        <div class="container header-main">
            <a href="{{ route('produk.index') }}" class="logo"><span class="leaf">🌿</span> Eco<b>Craft</b></a>
            <div class="searchbar">
                <form action="{{ route('produk.index') }}" method="get">
                    <input type="text" name="q" placeholder="Cari meja belajar, rak buku, kursi cafe..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Cari">🔍</button>
                </form>
            </div>
            <div class="header-actions">
                <a href="{{ route('keranjang.index') }}" class="icon-btn cart-wrap">
                    🛒 <span class="label">Keranjang</span>
                    <span class="cart-badge" id="cartBadge" {{ ($cartCount ?? 0) ? '' : 'style=display:none' }}>{{ $cartCount ?? 0 }}</span>
                </a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="icon-btn">📊 <span class="label">Dashboard</span></a>
                    @endif
                    <a href="#" class="icon-btn solid">👤 <span class="label">{{ Str::limit(auth()->user()->name, 12) }}</span></a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-btn" type="submit" style="border:none;background:none;cursor:pointer;font-family:inherit;">↩ <span class="label">Keluar</span></button></form>
                @else
                    <a href="{{ route('login') }}" class="icon-btn">Masuk</a>
                    <a href="{{ route('register') }}" class="icon-btn solid">Daftar</a>
                @endauth
            </div>
        </div>
        <div class="catnav">
            <div class="container">
                <a href="{{ route('produk.index') }}" class="cat-pill {{ request('kategori') ? '' : 'active' }}">🏠 Semua</a>
                @foreach (($navKategori ?? collect()) as $k)
                    <a href="{{ route('produk.index', ['kategori' => $k->slug]) }}" class="cat-pill {{ request('kategori') === $k->slug ? 'active' : '' }}">{{ $k->nama }}</a>
                @endforeach
            </div>
        </div>
    </header>

    @if (session('sukses'))
        <div class="toast sukses" data-toast>✅ {{ session('sukses') }}</div>
    @elseif (session('error'))
        <div class="toast error" data-toast>⚠️ {{ session('error') }}</div>
    @endif

    @yield('content')

    <footer>
        <div class="container">
            <div class="foot-grid">
                <div>
                    <h4 style="color:#fff;font-size:1.2rem;">🌿 {{ config('toko.nama') }}</h4>
                    <p>{{ config('toko.tagline') }}. Setiap pembelian Anda membantu mengurangi limbah kayu.</p>
                    <a href="https://instagram.com/{{ config('toko.instagram') }}" target="_blank" rel="noopener" style="font-weight:600;color:#fff;">📷 @{{ config('toko.instagram') }}</a>
                </div>
                <div><h4>Belanja</h4><a href="{{ route('produk.index') }}">Semua Produk</a><a href="#">Produk Terlaris</a><a href="#">Promo & Bundle</a></div>
                <div><h4>Bantuan</h4><a href="#">Cara Belanja</a><a href="#">Pengiriman</a><a href="#">Kebijakan Retur</a></div>
                <div><h4>Hubungi Kami</h4><p>📧 {{ config('toko.email') }}</p><p>📱 {{ config('toko.telepon') }}</p><p>📷 @{{ config('toko.instagram') }}</p></div>
            </div>
        </div>
        <div class="foot-bottom">© {{ date('Y') }} {{ config('toko.nama') }} — Dibuat dengan ❤️ untuk bumi.</div>
    </footer>

    <script>
        // Reveal on scroll
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: .12 });
        document.querySelectorAll('.reveal').forEach((el, i) => {
            el.style.transitionDelay = (i % 6 * 60) + 'ms';
            io.observe(el);
        });

        // Auto-hide toast
        document.querySelectorAll('[data-toast]').forEach(t => {
            setTimeout(() => { t.style.transition = 'opacity .4s, transform .4s'; t.style.opacity = '0'; t.style.transform = 'translate(-50%,-16px)'; }, 2600);
            setTimeout(() => t.remove(), 3100);
        });
    </script>
    @yield('scripts')
</body>
</html>
