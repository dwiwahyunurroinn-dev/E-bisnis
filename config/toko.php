<?php

return [
    'nama'      => env('TOKO_NAMA', 'Eco Craft'),
    'tagline'   => env('TOKO_TAGLINE', 'Furnitur & Kriya Kayu Daur Ulang'),
    'instagram' => env('TOKO_INSTAGRAM', 'ecocraft.id'),
    'email'     => env('TOKO_EMAIL', 'halo@ecocraft.id'),
    'telepon'   => env('TOKO_TELEPON', '0877-3477-7846'),
    // Nomor WhatsApp admin (format internasional tanpa + untuk wa.me).
    'whatsapp'      => env('TOKO_WHATSAPP', '6287734777846'),
    'whatsapp_text' => env('TOKO_WHATSAPP_TEXT', 'Halo Admin Eco Craft, saya butuh bantuan.'),
    // Batas waktu pembayaran sebelum pesanan dibatalkan otomatis (jam).
    'pesanan_expire_jam' => (int) env('TOKO_PESANAN_EXPIRE_JAM', 24),

    // Kredensial admin awal untuk seeder. WAJIB set ADMIN_PASSWORD di .env produksi!
    'admin_email'    => env('ADMIN_EMAIL', 'admin@ecocraft.id'),
    'admin_password' => env('ADMIN_PASSWORD', 'password'),
];
