<?php

/*
|--------------------------------------------------------------------------
| Kanal pembayaran (mode simulasi, gaya marketplace)
|--------------------------------------------------------------------------
| Dipakai saat Midtrans belum dikonfigurasi. Bila MIDTRANS_SERVER_KEY diisi,
| pembayaran non-COD dialihkan ke Midtrans Snap (yang juga menyediakan
| QRIS/VA/e-wallet asli), sedangkan COD tetap ditangani aplikasi.
*/

return [
    'kanal' => [
        'qris' => ['label' => 'QRIS', 'tipe' => 'qris', 'warna' => '#e23b53'],

        'va_bca'     => ['label' => 'BCA Virtual Account', 'tipe' => 'va', 'bank' => 'BCA', 'prefix' => '3901', 'warna' => '#1c48a5'],
        'va_bri'     => ['label' => 'BRI Virtual Account', 'tipe' => 'va', 'bank' => 'BRI', 'prefix' => '26215', 'warna' => '#00529c'],
        'va_bni'     => ['label' => 'BNI Virtual Account', 'tipe' => 'va', 'bank' => 'BNI', 'prefix' => '8241', 'warna' => '#f26522'],
        'va_mandiri' => ['label' => 'Mandiri Virtual Account', 'tipe' => 'va', 'bank' => 'Mandiri', 'prefix' => '89508', 'warna' => '#ffb700'],

        'gopay'     => ['label' => 'GoPay', 'tipe' => 'ewallet', 'warna' => '#00aed6'],
        'ovo'       => ['label' => 'OVO', 'tipe' => 'ewallet', 'warna' => '#4c3494'],
        'dana'      => ['label' => 'DANA', 'tipe' => 'ewallet', 'warna' => '#108ee9'],
        'shopeepay' => ['label' => 'ShopeePay', 'tipe' => 'ewallet', 'warna' => '#ee4d2d'],

        'cod' => ['label' => 'Bayar di Tempat (COD)', 'tipe' => 'cod', 'warna' => '#03734a'],
    ],
];
