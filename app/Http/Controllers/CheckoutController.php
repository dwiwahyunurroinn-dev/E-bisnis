<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Alamat;
use App\Models\DetailPesanan;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\User;
use App\Services\CartService;
use App\Services\OngkirService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OngkirService $ongkir,
    ) {}

    /** Halaman single-page checkout. */
    public function index(): View|RedirectResponse
    {
        if ($this->cart->kosong()) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        return view('checkout.index', [
            'items'        => $this->cart->items(),
            'subtotal'     => $this->cart->subtotal(),
            'diskon'       => $this->cart->diskonTotal(),
            'voucher'      => $this->cart->voucher(),
            'namaBundle'   => $this->cart->namaBundle(),
            'beratGram'    => $this->cart->beratGram(),
            'opsiOngkir'   => $this->ongkir->opsi(null, $this->cart->beratGram()),
        ]);
    }

    /** Proses pembuatan pesanan. */
    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->kosong()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang kosong.');
        }

        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email', 'max:150'],
            'telepon'        => ['required', 'string', 'max:25'],
            'kota'           => ['required', 'string', 'max:80'],
            'alamat_lengkap' => ['required', 'string'],
            'kode_pos'       => ['nullable', 'string', 'max:10'],
            'pengiriman'     => ['required', 'string'], // format: "kurir|layanan"
        ]);

        [$kurir, $layanan] = array_pad(explode('|', $data['pengiriman'], 2), 2, '');
        $opsi = $this->ongkir->cari($data['kota'], $this->cart->beratGram(), $kurir, $layanan);

        if (! $opsi) {
            return back()->withInput()->with('error', 'Opsi pengiriman tidak valid.');
        }

        $items    = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $diskon   = $this->cart->diskonTotal();
        $voucher  = $this->cart->voucher();
        $total    = max(0, $subtotal - $diskon) + $opsi['ongkir'];

        $pesanan = DB::transaction(function () use ($data, $items, $subtotal, $diskon, $voucher, $opsi, $total, $kurir) {
            // Jika sudah login pakai akun tsb, jika tidak (guest) cari/buat via email.
            $user = auth()->user() ?? User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['nama'], 'password' => bcrypt(Str::random(16))],
            );

            $alamat = Alamat::create([
                'user_id'        => $user->id,
                'label'          => 'Pengiriman',
                'penerima'       => $data['nama'],
                'telepon'        => $data['telepon'],
                'kota'           => $data['kota'],
                'alamat_lengkap' => $data['alamat_lengkap'],
                'kode_pos'       => $data['kode_pos'] ?? null,
            ]);

            $pesanan = Pesanan::create([
                'kode'         => $this->buatKode(),
                'user_id'      => $user->id,
                'alamat_id'    => $alamat->id,
                'kurir'        => $kurir,
                'layanan'      => $opsi['layanan'],
                'subtotal'     => $subtotal,
                'diskon'       => $diskon,
                'kode_voucher' => $voucher?->kode,
                'ongkir'       => $opsi['ongkir'],
                'total'        => $total,
                'status'       => 'pending',
            ]);

            // Catat pemakaian voucher.
            if ($voucher) {
                $voucher->increment('terpakai');
            }

            foreach ($items as $i) {
                DetailPesanan::create([
                    'pesanan_id'  => $pesanan->id,
                    'produk_id'   => $i['produk']->id,
                    'nama_produk' => $i['produk']->nama,
                    'harga'       => $i['produk']->harga,
                    'jumlah'      => $i['qty'],
                    'subtotal'    => $i['subtotal'],
                ]);
            }

            return $pesanan;
        });

        $this->cart->kosongkan();
        ActivityLog::catat('pesanan baru', $pesanan->kode, 'Total Rp'.number_format($total, 0, ',', '.'));

        // Notifikasi ke admin
        Notifikasi::keSemuaAdmin(
            'Pesanan baru masuk',
            $pesanan->kode.' senilai Rp'.number_format($total, 0, ',', '.'),
            route('admin.pesanan.show', $pesanan),
            'pesanan',
        );
        // Notifikasi ke pelanggan
        Notifikasi::kirim($pesanan->user_id, 'Pesanan dibuat',
            'Pesanan '.$pesanan->kode.' menunggu pembayaran.',
            route('pesanan.show', $pesanan->kode), 'status');

        return redirect()->route('pesanan.show', $pesanan->kode);
    }

    private function buatKode(): string
    {
        do {
            $kode = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (Pesanan::where('kode', $kode)->exists());

        return $kode;
    }
}
