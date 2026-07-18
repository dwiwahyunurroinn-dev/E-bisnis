<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'pertanyaan' => 'Berapa lama waktu pengiriman?',
                'jawaban' => 'Estimasi pengiriman 2-5 hari kerja tergantung kota tujuan dan kurir yang dipilih saat checkout.',
                'kata_kunci' => 'ongkir,ongkos kirim,pengiriman,kirim,estimasi,sampai',
                'kategori' => 'Pengiriman',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Metode pembayaran apa saja yang tersedia?',
                'jawaban' => 'Kami menerima pembayaran via Midtrans (transfer bank, e-wallet, kartu kredit) langsung dari halaman checkout.',
                'kata_kunci' => 'bayar,pembayaran,payment,transfer,midtrans',
                'kategori' => 'Pembayaran',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Apakah bisa retur atau tukar barang?',
                'jawaban' => 'Barang bisa diretur dalam 3 hari sejak diterima jika ada cacat produksi. Silakan hubungi admin dengan foto bukti kerusakan.',
                'kata_kunci' => 'retur,tukar,kembali,cacat,rusak,komplain',
                'kategori' => 'Retur',
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Bagaimana cara melacak status pesanan saya?',
                'jawaban' => 'Buka menu "Akun Saya" lalu "Pesanan Saya", atau klik "Lacak Pesanan" di bagian atas halaman untuk melihat status terbaru.',
                'kata_kunci' => 'lacak,status pesanan,cek pesanan,resi',
                'kategori' => 'Pesanan',
                'urutan' => 4,
            ],
            [
                'pertanyaan' => 'Bahan furnitur terbuat dari apa?',
                'jawaban' => 'Semua produk kami dibuat dari kayu daur ulang (palet, sisa konstruksi) yang diproses ulang agar kokoh dan ramah lingkungan.',
                'kata_kunci' => 'bahan,material,kayu,daur ulang,ramah lingkungan',
                'kategori' => 'Produk',
                'urutan' => 5,
            ],
            [
                'pertanyaan' => 'Apakah ada voucher atau promo saat ini?',
                'jawaban' => 'Cek kode voucher aktif di halaman keranjang, atau pantau info promo terbaru kami di Instagram.',
                'kata_kunci' => 'voucher,promo,diskon,potongan',
                'kategori' => 'Promo',
                'urutan' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['pertanyaan' => $faq['pertanyaan']], $faq + ['aktif' => true]);
        }
    }
}
