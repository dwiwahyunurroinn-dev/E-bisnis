@props(['name' => 'dot', 'size' => 20])

@php
    // Ikon garis modern (gaya Lucide/Feather). stroke = currentColor.
    $stroke = [
        'search'      => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'cart'        => '<circle cx="9" cy="21" r="1.6"/><circle cx="19" cy="21" r="1.6"/><path d="M2.5 3h2.2l2.2 12.4a1.6 1.6 0 0 0 1.6 1.3h9.1a1.6 1.6 0 0 0 1.6-1.3L21.5 7H6"/>',
        'user'        => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'home'        => '<path d="M3 9.5 12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5H4.5A1.5 1.5 0 0 1 3 20z"/><path d="M9 21v-7h6v7"/>',
        'leaf'        => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.4 5.1-6"/>',
        'recycle'     => '<path d="M7 19H4.8a1.8 1.8 0 0 1-1.5-2.7L5 13"/><path d="m9 9-2.6 1.5"/><path d="M14 5l1.2-2a1.8 1.8 0 0 1 3 .1L20 6"/><path d="M9.3 9.5 7.5 6"/><path d="M17 14l1.7.9a1.8 1.8 0 0 1-.2 3.2L16 19"/><path d="M14.5 14.5 16 18"/>',
        'truck'       => '<path d="M3 6.5A1.5 1.5 0 0 1 4.5 5H15v10H3z"/><path d="M15 8h3.5L21 11v4h-6"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17.5" cy="17.5" r="1.8"/>',
        'shield'      => '<path d="M12 3 5 6v5c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'card'        => '<rect x="2" y="5" width="20" height="14" rx="2.4"/><line x1="2" y1="9.5" x2="22" y2="9.5"/>',
        'bell'        => '<path d="M18 8a6 6 0 1 0-12 0c0 6-2.5 8-2.5 8h17S18 14 18 8"/><path d="M10.5 20a1.8 1.8 0 0 0 3 0"/>',
        'package'     => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>',
        'box'         => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>',
        'chart'       => '<line x1="6" y1="20" x2="6" y2="13"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="18" y1="20" x2="18" y2="9"/>',
        'tag'         => '<path d="M20.5 13.3 13 20.8a1.6 1.6 0 0 1-2.3 0L3.5 13.5V4h9.5l7.5 7.5a1.6 1.6 0 0 1 0 1.8"/><circle cx="8" cy="8" r="1.4"/>',
        'users'       => '<path d="M16 20v-2a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18v2"/><circle cx="10" cy="7.5" r="3.2"/><path d="M20 20v-2a3.5 3.5 0 0 0-2.7-3.4"/><path d="M15.5 4.2A3.2 3.2 0 0 1 15.5 11"/>',
        'key'         => '<circle cx="7.5" cy="15.5" r="3.5"/><path d="m10 13 8-8"/><path d="m15.5 7.5 2 2"/><path d="m18 5 2 2"/>',
        'clipboard'   => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4V3h6v1"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="13" y2="14"/>',
        'store'       => '<path d="M4 9V6.5L5.5 4h13L20 6.5V9"/><path d="M4 9a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"/><path d="M5 10.5V20h14v-9.5"/>',
        'logout'      => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'plus'        => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'minus'       => '<line x1="5" y1="12" x2="19" y2="12"/>',
        'edit'        => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash'       => '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        'left'        => '<polyline points="15 18 9 12 15 6"/>',
        'right'       => '<polyline points="9 18 15 12 9 6"/>',
        'sofa'        => '<path d="M4 11V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3"/><path d="M3 11a2 2 0 0 1 2 2v2h14v-2a2 2 0 0 1 4 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 1-1.7"/><line x1="6" y1="19" x2="6" y2="21"/><line x1="18" y1="19" x2="18" y2="21"/>',
        'instagram'   => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.2" fill="currentColor" stroke="none"/>',
        'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'phone'       => '<path d="M5 4h3l1.5 5-2 1.5a11 11 0 0 0 5 5l1.5-2 5 1.5V18a2 2 0 0 1-2 2A16 16 0 0 1 4 6a2 2 0 0 1 1-2"/>',
        'menu'        => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
        'check'       => '<polyline points="20 6 9 17 4 12"/>',
        'check-circle'=> '<circle cx="12" cy="12" r="9"/><polyline points="8.5 12 11 14.5 16 9.5"/>',
        'x'           => '<line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/>',
        'arrow-right' => '<line x1="4" y1="12" x2="20" y2="12"/><polyline points="14 6 20 12 14 18"/>',
        'pin'         => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11"/><circle cx="12" cy="10" r="2.5"/>',
        'eye'         => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7"/><circle cx="12" cy="12" r="3"/>',
        'settings'    => '<circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.3 1a7 7 0 0 0-1.7-1L14.5 2h-5l-.4 2.6a7 7 0 0 0-1.7 1l-2.3-1-2 3.4 2 1.5a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.3-1a7 7 0 0 0 1.7 1l.4 2.6h5l.4-2.6a7 7 0 0 0 1.7-1l2.3 1 2-3.4-2-1.5a7 7 0 0 0 .1-1"/>',
        'download'    => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        'dashboard'   => '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/>',
        'alert'       => '<path d="M12 3 2 20h20z"/><line x1="12" y1="10" x2="12" y2="14"/><circle cx="12" cy="17" r="0.6" fill="currentColor" stroke="none"/>',
        'clock'       => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/>',
        'dot'         => '<circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/>',
    ];
    $fill = [
        'star' => '<path d="M12 2.5l2.95 6 6.6.96-4.78 4.66 1.13 6.58L12 17.6l-5.9 3.1L7.23 14.1 2.45 9.46l6.6-.96z" fill="currentColor" stroke="none"/>',
    ];
    $isFill = array_key_exists($name, $fill);
    $inner = $isFill ? $fill[$name] : ($stroke[$name] ?? $stroke['dot']);
@endphp

<svg {{ $attributes->merge(['class' => 'ico']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="{{ $isFill ? 'none' : 'currentColor' }}" stroke-width="1.8"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $inner !!}</svg>
