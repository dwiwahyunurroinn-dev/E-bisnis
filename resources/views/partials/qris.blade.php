{{-- QR simulasi deterministik dari kode pesanan (mode demo, bukan QRIS asli). --}}
@php
    $n = 25;
    $bits = '';
    $h = md5($seed);
    while (strlen($bits) < $n * $n) {
        $h = md5($h);
        foreach (str_split($h) as $c) {
            $bits .= str_pad(base_convert($c, 16, 2), 4, '0', STR_PAD_LEFT);
        }
    }
    $finder = function ($r, $c) {
        // Pola kotak penanda 7x7 khas QR di tiga sudut.
        foreach ([[0, 0], [0, 18], [18, 0]] as [$fr, $fc]) {
            if ($r >= $fr && $r < $fr + 7 && $c >= $fc && $c < $fc + 7) {
                $lr = $r - $fr; $lc = $c - $fc;
                $ring = ($lr === 0 || $lr === 6 || $lc === 0 || $lc === 6);
                $inti = ($lr >= 2 && $lr <= 4 && $lc >= 2 && $lc <= 4);
                return $ring || $inti;
            }
        }
        return null;
    };
@endphp
<svg viewBox="0 0 {{ $n }} {{ $n }}" width="{{ $ukuran ?? 190 }}" height="{{ $ukuran ?? 190 }}"
     style="shape-rendering:crispEdges;background:#fff;border-radius:8px;" aria-label="Kode QR simulasi">
    @for ($r = 0; $r < $n; $r++)
        @for ($c = 0; $c < $n; $c++)
            @php $f = $finder($r, $c); $isi = $f ?? ($bits[$r * $n + $c] === '1'); @endphp
            @if ($isi)<rect x="{{ $c }}" y="{{ $r }}" width="1" height="1" fill="#1f2a37"/>@endif
        @endfor
    @endfor
</svg>
