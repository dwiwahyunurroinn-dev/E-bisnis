@props(['size' => 120])

{{-- Maskot EcoCraft: "Tunas", si tunas kayu daur ulang yang ramah --}}
<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 160 160" fill="none" aria-label="Maskot EcoCraft">
    <defs>
        <linearGradient id="mBody" x1="40" y1="40" x2="120" y2="140" gradientUnits="userSpaceOnUse">
            <stop stop-color="#caa06a"/><stop offset="1" stop-color="#a87b46"/>
        </linearGradient>
        <linearGradient id="mLeaf" x1="60" y1="6" x2="110" y2="46" gradientUnits="userSpaceOnUse">
            <stop stop-color="#22c378"/><stop offset="1" stop-color="#048a55"/>
        </linearGradient>
    </defs>

    {{-- bayangan --}}
    <ellipse cx="80" cy="146" rx="40" ry="7" fill="#04864b" opacity=".12"/>

    {{-- daun tunas di kepala --}}
    <path d="M80 40C80 22 90 10 108 8c2 16-6 30-22 33" fill="url(#mLeaf)"/>
    <path d="M80 42C80 26 70 15 54 14c-1 14 7 26 22 29" fill="#15c272"/>
    <line x1="80" y1="58" x2="80" y2="40" stroke="#048a55" stroke-width="4" stroke-linecap="round"/>

    {{-- badan (balok kayu membulat) --}}
    <rect x="36" y="52" width="88" height="84" rx="30" fill="url(#mBody)"/>
    {{-- serat kayu --}}
    <path d="M50 74c8 3 16 3 24 0M50 92c10 4 22 4 32 0M86 110c8 2 16 2 22 0" stroke="#8a6233" stroke-width="3" stroke-linecap="round" opacity=".5"/>

    {{-- wajah --}}
    <circle cx="64" cy="92" r="8" fill="#fff"/><circle cx="66" cy="93" r="4" fill="#2b3a30"/>
    <circle cx="96" cy="92" r="8" fill="#fff"/><circle cx="98" cy="93" r="4" fill="#2b3a30"/>
    <circle cx="54" cy="104" r="5" fill="#e8896b" opacity=".5"/>
    <circle cx="106" cy="104" r="5" fill="#e8896b" opacity=".5"/>
    <path d="M72 108c4 4 12 4 16 0" stroke="#2b3a30" stroke-width="3.4" stroke-linecap="round"/>

    {{-- tangan kecil melambai --}}
    <circle cx="30" cy="98" r="9" fill="#a87b46"/>
    <circle cx="130" cy="98" r="9" fill="#a87b46"/>
</svg>
