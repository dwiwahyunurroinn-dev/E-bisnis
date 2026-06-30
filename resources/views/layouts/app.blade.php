<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kayu Reclaimed — Furnitur Kayu Daur Ulang')</title>
    <style>
        /* ---- Palet earth tone (konsep kayu daur ulang) ---- */
        :root {
            --bg:        #f5efe6;
            --surface:   #fffaf3;
            --kayu:      #8b5e3c;
            --kayu-tua:  #5c3d21;
            --daun:      #6b7a4f;
            --teks:      #3b2f25;
            --teks-soft: #7a6a59;
            --garis:     #e3d8c8;
            --shadow:    0 6px 20px rgba(92, 61, 33, .08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--teks);
            line-height: 1.6;
        }
        a { color: inherit; text-decoration: none; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }

        /* ---- Header ---- */
        header {
            background: var(--kayu-tua);
            color: var(--surface);
            padding: 16px 0;
            position: sticky; top: 0; z-index: 10;
            box-shadow: var(--shadow);
        }
        header .container { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .logo { font-size: 1.35rem; font-weight: 700; letter-spacing: .5px; }
        .logo span { color: #d9b382; }
        .search { display: flex; gap: 8px; flex: 1; max-width: 420px; }
        .search input {
            flex: 1; padding: 9px 14px; border: none; border-radius: 8px;
            background: rgba(255,255,255,.92); color: var(--teks);
        }
        .search button {
            padding: 9px 16px; border: none; border-radius: 8px; cursor: pointer;
            background: var(--daun); color: #fff; font-weight: 600;
        }

        /* ---- Hero ---- */
        .hero {
            background: linear-gradient(120deg, #efe3d2, #e7d6bd);
            padding: 48px 0; text-align: center;
        }
        .hero h1 { font-size: 2.1rem; color: var(--kayu-tua); margin-bottom: 8px; }
        .hero p { color: var(--teks-soft); max-width: 560px; margin: 0 auto; }

        /* ---- Filter kategori ---- */
        .chips { display: flex; flex-wrap: wrap; gap: 10px; margin: 28px 0 8px; }
        .chip {
            padding: 7px 16px; border-radius: 999px; border: 1px solid var(--garis);
            background: var(--surface); color: var(--teks-soft); font-size: .9rem;
            transition: all .15s;
        }
        .chip:hover { border-color: var(--kayu); color: var(--kayu); }
        .chip.active { background: var(--kayu); color: #fff; border-color: var(--kayu); }

        /* ---- Grid produk ---- */
        .grid {
            display: grid; gap: 22px; margin: 26px 0 40px;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }
        .card {
            background: var(--surface); border-radius: 14px; overflow: hidden;
            box-shadow: var(--shadow); transition: transform .18s, box-shadow .18s;
            display: flex; flex-direction: column;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(92,61,33,.16); }
        .thumb {
            aspect-ratio: 4 / 3; width: 100%; object-fit: cover;
            background: linear-gradient(135deg, #d8c3a5, #b8997099);
        }
        .card-body { padding: 14px 16px 18px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
        .kat { font-size: .75rem; text-transform: uppercase; letter-spacing: .6px; color: var(--daun); font-weight: 600; }
        .nama { font-size: 1rem; font-weight: 600; color: var(--teks); }
        .harga { font-size: 1.1rem; font-weight: 700; color: var(--kayu-tua); margin-top: auto; }
        .stok { font-size: .78rem; }
        .stok.ada { color: var(--daun); }
        .stok.habis { color: #b14b3c; }
        .badge-habis {
            position: absolute; top: 10px; left: 10px; background: #b14b3c; color: #fff;
            font-size: .72rem; padding: 3px 9px; border-radius: 6px;
        }
        .card .thumb-wrap { position: relative; }

        /* ---- Detail produk ---- */
        .detail { display: grid; grid-template-columns: 1fr 1fr; gap: 36px; margin: 34px 0; }
        .detail img { width: 100%; border-radius: 14px; box-shadow: var(--shadow); }
        .detail h1 { color: var(--kayu-tua); margin-bottom: 6px; }
        .detail .harga { font-size: 1.6rem; margin: 14px 0; }
        .meta { margin: 6px 0; color: var(--teks-soft); font-size: .92rem; }
        .btn {
            display: inline-block; margin-top: 18px; padding: 12px 26px; border: none;
            border-radius: 10px; background: var(--daun); color: #fff; font-weight: 600;
            cursor: pointer; font-size: 1rem;
        }
        .btn:disabled { background: #b9b2a6; cursor: not-allowed; }
        .back { display: inline-block; margin: 24px 0 0; color: var(--teks-soft); }

        /* ---- Pagination & footer ---- */
        .pagination { display: flex; gap: 6px; flex-wrap: wrap; list-style: none; margin: 10px 0 50px; }
        .pagination a, .pagination span {
            padding: 7px 13px; border-radius: 8px; background: var(--surface);
            border: 1px solid var(--garis); font-size: .9rem;
        }
        footer { background: var(--kayu-tua); color: #d9c9b4; padding: 26px 0; text-align: center; font-size: .88rem; }

        .empty { text-align: center; padding: 60px 0; color: var(--teks-soft); }

        @media (max-width: 720px) {
            .detail { grid-template-columns: 1fr; }
            header .container { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="{{ route('produk.index') }}" class="logo">Kayu<span>Reclaimed</span></a>
            <form class="search" action="{{ route('produk.index') }}" method="get">
                <input type="text" name="q" placeholder="Cari meja, rak, kursi..." value="{{ request('q') }}">
                <button type="submit">Cari</button>
            </form>
        </div>
    </header>

    @yield('content')

    <footer>
        <div class="container">
            &copy; {{ date('Y') }} KayuReclaimed — Furnitur dari kayu daur ulang. Dibuat dengan ❤️ untuk bumi.
        </div>
    </footer>
</body>
</html>
