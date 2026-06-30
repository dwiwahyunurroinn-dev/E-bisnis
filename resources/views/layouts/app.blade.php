<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KayuReclaimed — Marketplace Furnitur Kayu Daur Ulang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      #0c7c59;   /* hijau eco (identitas daur ulang) */
            --primary-dark: #095c42;
            --primary-soft: #e6f4ee;
            --accent:       #f5a623;   /* aksen hangat */
            --danger:       #e23744;
            --ink:          #1c2b27;
            --ink-soft:     #5a6b65;
            --line:         #eceef0;
            --bg:           #f4f6f8;
            --white:        #ffffff;
            --radius:       14px;
            --shadow-sm:    0 1px 3px rgba(16,40,34,.06);
            --shadow-md:    0 6px 20px rgba(16,40,34,.10);
            --shadow-lg:    0 14px 40px rgba(16,40,34,.16);
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

        /* ---------- Top bar ---------- */
        .topbar { background: var(--primary-dark); color: #d9ece4; font-size: .8rem; }
        .topbar .container { display: flex; justify-content: space-between; align-items: center; height: 34px; }
        .topbar a { opacity: .9; }
        .topbar a:hover { opacity: 1; }

        /* ---------- Header ---------- */
        header { background: var(--white); position: sticky; top: 0; z-index: 50; box-shadow: var(--shadow-sm); }
        .header-main { display: flex; align-items: center; gap: 24px; height: 72px; }
        .logo { display: flex; align-items: center; gap: 9px; font-size: 1.35rem; font-weight: 800; color: var(--primary); white-space: nowrap; }
        .logo .leaf { width: 34px; height: 34px; display: grid; place-items: center; background: var(--primary); color: #fff; border-radius: 10px; font-size: 1.05rem; }
        .logo b { color: var(--ink); font-weight: 800; }

        .searchbar { flex: 1; max-width: 640px; position: relative; }
        .searchbar form { display: flex; }
        .searchbar input {
            width: 100%; height: 46px; padding: 0 52px 0 18px; font-size: .95rem;
            border: 1.8px solid var(--line); border-radius: 12px; outline: none;
            transition: border-color .15s; background: #fafbfc; font-family: inherit;
        }
        .searchbar input:focus { border-color: var(--primary); background: #fff; }
        .searchbar button {
            position: absolute; right: 6px; top: 6px; height: 34px; width: 40px;
            border: none; border-radius: 9px; background: var(--primary); color: #fff;
            cursor: pointer; display: grid; place-items: center; transition: background .15s;
        }
        .searchbar button:hover { background: var(--primary-dark); }

        .header-actions { display: flex; align-items: center; gap: 8px; }
        .icon-btn {
            display: flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 10px;
            color: var(--ink-soft); font-weight: 600; font-size: .9rem; transition: background .15s, color .15s;
        }
        .icon-btn:hover { background: var(--primary-soft); color: var(--primary-dark); }
        .icon-btn.solid { background: var(--primary); color: #fff; }
        .icon-btn.solid:hover { background: var(--primary-dark); color: #fff; }
        .cart-wrap { position: relative; }
        .cart-badge {
            position: absolute; top: 2px; right: 6px; background: var(--danger); color: #fff;
            font-size: .65rem; font-weight: 700; min-width: 17px; height: 17px; border-radius: 9px;
            display: grid; place-items: center; padding: 0 4px;
        }

        /* ---------- Category nav ---------- */
        .catnav { background: var(--white); border-top: 1px solid var(--line); position: sticky; top: 72px; z-index: 40; }
        .catnav .container { display: flex; gap: 8px; overflow-x: auto; padding: 10px 20px; scrollbar-width: none; }
        .catnav .container::-webkit-scrollbar { display: none; }
        .cat-pill {
            white-space: nowrap; padding: 8px 16px; border-radius: 999px; font-size: .88rem; font-weight: 600;
            color: var(--ink-soft); background: var(--bg); transition: all .15s;
        }
        .cat-pill:hover { color: var(--primary-dark); background: var(--primary-soft); }
        .cat-pill.active { background: var(--primary); color: #fff; }

        /* ---------- Hero ---------- */
        .hero { padding: 26px 0 6px; }
        .hero-banner {
            border-radius: 20px; overflow: hidden; position: relative; color: #fff;
            background: linear-gradient(120deg, #0c7c59 0%, #0a6b4d 45%, #145c44 100%);
            padding: 44px 48px; box-shadow: var(--shadow-md);
        }
        .hero-banner::after {
            content: "🪵"; position: absolute; right: 40px; bottom: -20px; font-size: 11rem; opacity: .12; transform: rotate(-12deg);
        }
        .hero-banner h1 { font-size: 2.1rem; font-weight: 800; line-height: 1.2; max-width: 560px; }
        .hero-banner p { margin-top: 10px; opacity: .92; max-width: 480px; }
        .hero-cta { display: inline-flex; margin-top: 20px; background: #fff; color: var(--primary-dark); font-weight: 700; padding: 12px 26px; border-radius: 11px; }

        /* ---------- Trust badges ---------- */
        .trust { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin: 22px 0 8px; }
        .trust-item { background: var(--white); border-radius: var(--radius); padding: 16px 18px; display: flex; gap: 12px; align-items: center; box-shadow: var(--shadow-sm); }
        .trust-item .t-ico { font-size: 1.5rem; }
        .trust-item b { display: block; font-size: .9rem; }
        .trust-item span { font-size: .78rem; color: var(--ink-soft); }

        /* ---------- Section heading ---------- */
        .section-head { display: flex; align-items: center; justify-content: space-between; margin: 30px 0 16px; }
        .section-head h2 { font-size: 1.3rem; font-weight: 800; }
        .section-head .sub { font-size: .85rem; color: var(--ink-soft); }

        /* ---------- Product grid ---------- */
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); margin-bottom: 36px; }
        .pcard {
            background: var(--white); border-radius: var(--radius); overflow: hidden; position: relative;
            box-shadow: var(--shadow-sm); transition: transform .18s, box-shadow .18s; display: flex; flex-direction: column;
            border: 1px solid var(--line);
        }
        .pcard:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: transparent; }
        .pthumb { position: relative; aspect-ratio: 1/1; background: linear-gradient(135deg,#eef2f0,#dfe8e3); display: grid; place-items: center; overflow: hidden; }
        .pthumb .ph-emoji { font-size: 3.4rem; opacity: .55; }
        .pthumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .badge-disc { position: absolute; top: 10px; left: 10px; background: var(--danger); color: #fff; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 7px; z-index: 2; }
        .badge-soldout { position: absolute; inset: 0; background: rgba(255,255,255,.72); display: grid; place-items: center; z-index: 3; font-weight: 700; color: var(--ink-soft); }
        .wish { position: absolute; top: 8px; right: 8px; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,.92); display: grid; place-items: center; z-index: 2; color: var(--ink-soft); box-shadow: var(--shadow-sm); transition: color .15s; }
        .wish:hover { color: var(--danger); }
        .pbody { padding: 12px 13px 15px; display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .pcat { font-size: .7rem; text-transform: uppercase; letter-spacing: .5px; color: var(--primary); font-weight: 700; }
        .pname { font-size: .9rem; font-weight: 600; color: var(--ink); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5em; }
        .pprice { font-size: 1.08rem; font-weight: 800; color: var(--ink); margin-top: 2px; }
        .pprice-old { font-size: .76rem; color: var(--ink-soft); text-decoration: line-through; }
        .pmeta { display: flex; align-items: center; gap: 6px; font-size: .76rem; color: var(--ink-soft); margin-top: auto; padding-top: 6px; }
        .stars { color: var(--accent); font-weight: 700; }

        /* ---------- Pagination ---------- */
        nav[role="navigation"], .pagination-wrap { margin: 8px 0 50px; }
        .pagination { display: flex; gap: 6px; flex-wrap: wrap; list-style: none; justify-content: center; }
        .pagination a, .pagination span {
            min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            background: var(--white); border: 1px solid var(--line); font-size: .88rem; font-weight: 600; color: var(--ink-soft);
        }
        .pagination .active span, .pagination [aria-current] span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ---------- Detail ---------- */
        .breadcrumb { font-size: .82rem; color: var(--ink-soft); margin: 18px 0 14px; display: flex; gap: 7px; flex-wrap: wrap; }
        .breadcrumb a:hover { color: var(--primary); }
        .detail { display: grid; grid-template-columns: 1fr 1.15fr 0.9fr; gap: 24px; align-items: start; }
        .detail-gallery { background: var(--white); border-radius: var(--radius); padding: 16px; box-shadow: var(--shadow-sm); position: sticky; top: 130px; }
        .detail-main-img { aspect-ratio: 1/1; border-radius: 12px; background: linear-gradient(135deg,#eef2f0,#dfe8e3); display: grid; place-items: center; overflow: hidden; position: relative; }
        .detail-main-img .ph-emoji { font-size: 6rem; opacity: .55; }
        .detail-main-img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .detail-info { background: var(--white); border-radius: var(--radius); padding: 22px 24px; box-shadow: var(--shadow-sm); }
        .detail-info h1 { font-size: 1.45rem; font-weight: 800; line-height: 1.3; }
        .rating-row { display: flex; align-items: center; gap: 10px; margin: 10px 0 14px; font-size: .85rem; color: var(--ink-soft); }
        .price-box { background: var(--primary-soft); border-radius: 12px; padding: 16px 18px; margin: 6px 0 18px; }
        .price-now { font-size: 1.9rem; font-weight: 800; color: var(--primary-dark); }
        .price-was { font-size: .9rem; color: var(--ink-soft); text-decoration: line-through; margin-left: 8px; }
        .disc-chip { background: var(--danger); color: #fff; font-size: .72rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; margin-left: 8px; }
        .spec { display: flex; gap: 10px; padding: 9px 0; border-bottom: 1px dashed var(--line); font-size: .9rem; }
        .spec .k { width: 130px; color: var(--ink-soft); flex-shrink: 0; }
        .eco-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
        .eco-chip { background: var(--primary-soft); color: var(--primary-dark); font-size: .78rem; font-weight: 600; padding: 5px 12px; border-radius: 999px; }
        .desc { margin-top: 18px; font-size: .92rem; color: var(--ink-soft); line-height: 1.7; }

        /* Buy box */
        .buybox { background: var(--white); border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow-sm); position: sticky; top: 130px; }
        .buybox h3 { font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-soft); margin-bottom: 14px; }
        .qty { display: flex; align-items: center; gap: 0; border: 1.5px solid var(--line); border-radius: 10px; width: fit-content; overflow: hidden; }
        .qty button { width: 38px; height: 38px; border: none; background: var(--bg); font-size: 1.2rem; cursor: pointer; color: var(--ink); }
        .qty button:hover { background: var(--primary-soft); color: var(--primary-dark); }
        .qty input { width: 50px; height: 38px; text-align: center; border: none; font-size: 1rem; font-family: inherit; font-weight: 600; }
        .buybox .row { display: flex; justify-content: space-between; align-items: center; margin: 16px 0; font-size: .9rem; }
        .buybox .total { font-weight: 800; font-size: 1.25rem; color: var(--primary-dark); }
        .btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; border: none; border-radius: 11px; font-size: .98rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .15s; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: #fff; color: var(--primary-dark); border: 1.8px solid var(--primary); margin-top: 10px; }
        .btn-outline:hover { background: var(--primary-soft); }
        .btn:disabled { background: #cfd6d3; color: #fff; cursor: not-allowed; border: none; }

        /* ---------- Footer ---------- */
        footer { background: #10231d; color: #b6c8c0; margin-top: 30px; }
        .foot-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 30px; padding: 44px 0 30px; }
        .foot-grid h4 { color: #fff; font-size: .95rem; margin-bottom: 14px; }
        .foot-grid a, .foot-grid p { display: block; font-size: .85rem; margin-bottom: 9px; color: #b6c8c0; }
        .foot-grid a:hover { color: #fff; }
        .foot-bottom { border-top: 1px solid rgba(255,255,255,.1); padding: 18px 0; text-align: center; font-size: .8rem; color: #8fa39b; }

        .empty { text-align: center; padding: 70px 0; color: var(--ink-soft); }
        .empty .big { font-size: 3rem; margin-bottom: 10px; }

        @media (max-width: 900px) {
            .detail { grid-template-columns: 1fr; }
            .detail-gallery, .buybox { position: static; }
            .trust { grid-template-columns: repeat(2, 1fr); }
            .foot-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .header-actions .label { display: none; }
            .hero-banner { padding: 30px 24px; }
            .hero-banner h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container">
            <span>♻️ Furnitur dari 100% kayu daur ulang — ramah lingkungan</span>
            <span><a href="#">Bantuan</a> &nbsp;·&nbsp; <a href="#">Lacak Pesanan</a> &nbsp;·&nbsp; <a href="#">Tentang Kami</a></span>
        </div>
    </div>

    <header>
        <div class="container header-main">
            <a href="{{ route('produk.index') }}" class="logo">
                <span class="leaf">🌿</span> Kayu<b>Reclaimed</b>
            </a>
            <div class="searchbar">
                <form action="{{ route('produk.index') }}" method="get">
                    <input type="text" name="q" placeholder="Cari meja belajar, rak buku, kursi cafe..." value="{{ request('q') }}">
                    <button type="submit" aria-label="Cari">🔍</button>
                </form>
            </div>
            <div class="header-actions">
                <a href="#" class="icon-btn cart-wrap">
                    🛒 <span class="label">Keranjang</span>
                    <span class="cart-badge">0</span>
                </a>
                <a href="#" class="icon-btn solid">👤 <span class="label">Masuk</span></a>
            </div>
        </div>
        <div class="catnav">
            <div class="container">
                <a href="{{ route('produk.index') }}" class="cat-pill {{ request('kategori') ? '' : 'active' }}">🏠 Semua</a>
                @foreach (($navKategori ?? collect()) as $k)
                    <a href="{{ route('produk.index', ['kategori' => $k->slug]) }}"
                       class="cat-pill {{ request('kategori') === $k->slug ? 'active' : '' }}">{{ $k->nama }}</a>
                @endforeach
            </div>
        </div>
    </header>

    @yield('content')

    <footer>
        <div class="container">
            <div class="foot-grid">
                <div>
                    <h4 class="logo" style="color:#fff;">🌿 KayuReclaimed</h4>
                    <p>Marketplace furnitur berbahan kayu daur ulang. Setiap pembelian Anda membantu mengurangi limbah kayu.</p>
                </div>
                <div>
                    <h4>Belanja</h4>
                    <a href="{{ route('produk.index') }}">Semua Produk</a>
                    <a href="#">Produk Terlaris</a>
                    <a href="#">Promo & Bundle</a>
                </div>
                <div>
                    <h4>Bantuan</h4>
                    <a href="#">Cara Belanja</a>
                    <a href="#">Pengiriman</a>
                    <a href="#">Kebijakan Retur</a>
                </div>
                <div>
                    <h4>Hubungi Kami</h4>
                    <p>📧 halo@kayureclaimed.id</p>
                    <p>📱 0812-3456-7890</p>
                </div>
            </div>
        </div>
        <div class="foot-bottom">© {{ date('Y') }} KayuReclaimed — Dibuat dengan ❤️ untuk bumi.</div>
    </footer>
</body>
</html>
