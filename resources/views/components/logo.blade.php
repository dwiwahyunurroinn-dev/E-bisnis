@props(['size' => 34, 'light' => false, 'wordmark' => true])

@php
    // Logo unggahan admin (Pengaturan) menggantikan badge bawaan bila ada.
    $logoUpload = \App\Models\Pengaturan::ambil('logo');
    [$namaA, $namaB] = array_pad(explode(' ', config('toko.nama'), 2), 2, '');
@endphp

{{-- Logo toko: unggahan admin, atau badge daun + serat kayu bawaan --}}
<span {{ $attributes->merge(['class' => 'ec-logo']) }} style="display:inline-flex;align-items:center;gap:10px;">
    @if ($logoUpload)
        <img src="{{ asset('storage/'.$logoUpload) }}" alt="{{ config('toko.nama') }}"
             style="width:{{ $size }}px;height:{{ $size }}px;object-fit:contain;border-radius:{{ round($size*0.27) }}px;">
    @else
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" aria-label="{{ config('toko.nama') }}">
            <defs>
                <linearGradient id="ecg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#15c272"/><stop offset="1" stop-color="#048a55"/>
                </linearGradient>
            </defs>
            <rect width="48" height="48" rx="13" fill="url(#ecg)"/>
            {{-- daun --}}
            <path d="M33 13c0 10-6.5 16-15 16-1.6 0-3-.2-4.3-.6C14 19 21 14 33 13Z" fill="#fff" opacity=".96"/>
            {{-- tulang daun / serat kayu --}}
            <path d="M14 30c4-7 9-11 17-13" stroke="#048a55" stroke-width="2" stroke-linecap="round"/>
            {{-- tunas --}}
            <path d="M16 35c0-3 1.6-4.8 4.4-5.4" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/>
        </svg>
    @endif
    @if ($wordmark)
        <span style="font-weight:800;font-size:{{ round($size*0.5) }}px;letter-spacing:-.5px;color:{{ $light ? '#fff' : 'var(--primary-deep)' }};line-height:1;">
            {{ $namaA }}<span style="color:{{ $light ? 'rgba(255,255,255,.85)' : 'var(--ink)' }};">{{ $namaB }}</span>
        </span>
    @endif
</span>
