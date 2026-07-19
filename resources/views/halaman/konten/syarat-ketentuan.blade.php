<p>Dengan menggunakan situs {{ config('toko.nama') }}, Anda menyetujui ketentuan berikut:</p>

<h2>1. Akun</h2>
<ul>
    <li>Anda bertanggung jawab menjaga kerahasiaan kata sandi akun.</li>
    <li>Satu orang satu akun; akun yang disalahgunakan dapat dinonaktifkan.</li>
</ul>

<h2>2. Produk & harga</h2>
<ul>
    <li>Produk dibuat dari kayu daur ulang — warna dan serat tiap unit dapat sedikit
        berbeda dari foto; itu ciri khas, bukan cacat.</li>
    <li>Harga dan promo dapat berubah sewaktu-waktu; harga yang berlaku adalah harga
        saat checkout.</li>
</ul>

<h2>3. Pemesanan & pembayaran</h2>
<ul>
    <li>Pesanan yang tidak dibayar dalam {{ config('toko.pesanan_expire_jam', 24) }} jam
        dibatalkan otomatis dan stok dikembalikan.</li>
    <li>Untuk COD, pembeli wajib menyiapkan uang tunai sesuai total saat kurir tiba;
        penolakan tanpa alasan sah dapat membatasi akses COD berikutnya.</li>
</ul>

<h2>4. Pengiriman</h2>
<ul>
    <li>Estimasi pengiriman mengikuti layanan kurir yang dipilih dan bukan jaminan mutlak.</li>
    <li>Periksa kondisi paket saat diterima; kendala diproses sesuai
        <a href="{{ route('halaman', 'kebijakan-retur') }}">Kebijakan Retur</a>.</li>
</ul>

<h2>5. Konten pengguna</h2>
<ul>
    <li>Ulasan dan pesan chat tidak boleh memuat SARA, kata kasar, atau tautan berbahaya;
        pelanggaran dapat dihapus admin.</li>
</ul>

<p>Pertanyaan seputar ketentuan ini: hubungi
<a href="mailto:{{ config('toko.email') }}">{{ config('toko.email') }}</a>.</p>
