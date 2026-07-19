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
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OngkirService $ongkir,
        private readonly PesananService $pesananService,
    ) {}

    /** Halaman single-page checkout. */
    public function index(): View|RedirectResponse
    {
        if ($this->cart->kosong()) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        // Validasi stok sebelum menampilkan checkout.
        foreach ($this->cart->items() as $i) {
            if ($i['qty'] > $i['produk']->stok) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Stok "'.$i['produk']->nama.'" tidak mencukupi (sisa '.$i['produk']->stok.').');
            }
        }

        return view('checkout.index', [
            'items'        => $this->cart->items(),
            'subtotal'     => $this->cart->subtotal(),
            'diskon'       => $this->cart->diskonTotal(),
            'voucher'      => $this->cart->voucher(),
            'namaBundle'   => $this->cart->namaBundle(),
            'beratGram'    => $this->cart->beratGram(),
            'opsiOngkir'   => $this->ongkir->opsi(null, $this->cart->beratGram()),
            'user'         => auth()->user(),
            'alamatList'   => Alamat::where('user_id', auth()->id())->orderByDesc('utama')->latest()->get(),
        ]);
    }

    /** Proses pembuatan pesanan. */
    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->kosong()) {
            return redirect()->route('keranjang.index')->with('error', 'Keranjang kosong.');
        }

        // Jika pilih alamat tersimpan, salin datanya ke input.
        if ($request->filled('alamat_id')) {
            $tersimpan = Alamat::where('user_id', auth()->id())->find($request->alamat_id);
            if ($tersimpan) {
                $request->merge([
                    'nama'           => $tersimpan->penerima,
                    'telepon'        => $tersimpan->telepon,
                    'kota'           => $tersimpan->kota,
                    'alamat_lengkap' => $tersimpan->alamat_lengkap,
                    'kode_pos'       => $tersimpan->kode_pos,
                ]);
            }
        }

        $data = $request->validate([
            'nama'           => ['required', 'string', 'max:120'],
            'telepon'        => ['required', 'string', 'max:25'],
            'kota'           => ['required', 'string', 'max:80'],
            'alamat_lengkap' => ['required', 'string'],
            'kode_pos'       => ['nullable', 'string', 'max:10'],
            'pengiriman'     => ['required', 'string'], // format: "kurir|layanan"
            'pembayaran'     => ['nullable', Rule::in(array_keys(config('pembayaran.kanal')))],
            'alamat_id'      => ['nullable', 'integer'],
            'simpan_alamat'  => ['nullable', 'boolean'],
        ]);
        $metodeBayar = $data['pembayaran'] ?? 'qris';

        $items = $this->cart->items();

        // Validasi stok terkini (cegah overselling).
        foreach ($items as $i) {
            if ($i['qty'] > $i['produk']->stok) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Stok "'.$i['produk']->nama.'" tidak mencukupi.');
            }
        }

        [$kurir, $layanan] = array_pad(explode('|', $data['pengiriman'], 2), 2, '');
        $opsi = $this->ongkir->cari($data['kota'], $this->cart->beratGram(), $kurir, $layanan);

        if (! $opsi) {
            return back()->withInput()->with('error', 'Opsi pengiriman tidak valid.');
        }

        $subtotal = $this->cart->subtotal();
        $diskon   = $this->cart->diskonTotal();
        $voucher  = $this->cart->voucher();
        $total    = max(0, $subtotal - $diskon) + $opsi['ongkir'];

        $pesanan = DB::transaction(function () use ($data, $request, $items, $subtotal, $diskon, $voucher, $opsi, $total, $kurir, $metodeBayar) {
            // Checkout wajib login (standar marketplace) -> pakai akun aktif.
            $user = auth()->user();

            // Simpan ke address book bila diminta (alamat baru).
            if (empty($data['alamat_id']) && $request->boolean('simpan_alamat')) {
                $baru = Alamat::create([
                    'user_id'        => $user->id,
                    'label'          => 'Alamat',
                    'penerima'       => $data['nama'],
                    'telepon'        => $data['telepon'],
                    'kota'           => $data['kota'],
                    'alamat_lengkap' => $data['alamat_lengkap'],
                    'kode_pos'       => $data['kode_pos'] ?? null,
                    'utama'          => Alamat::where('user_id', $user->id)->count() === 0,
                ]);
                $data['alamat_id'] = $baru->id;
            }

            $pesanan = Pesanan::create([
                'kode'         => $this->buatKode(),
                'user_id'      => $user->id,
                'alamat_id'    => $data['alamat_id'] ?? null, // referensi address book (opsional)
                // Snapshot alamat pengiriman pada pesanan.
                'penerima'       => $data['nama'],
                'telepon'        => $data['telepon'],
                'kota'           => $data['kota'],
                'alamat_lengkap' => $data['alamat_lengkap'],
                'kode_pos'       => $data['kode_pos'] ?? null,
                'kurir'        => $kurir,
                'layanan'      => $opsi['layanan'],
                'subtotal'     => $subtotal,
                'diskon'       => $diskon,
                'kode_voucher' => $voucher?->kode,
                'ongkir'       => $opsi['ongkir'],
                'total'        => $total,
                'metode_bayar' => $metodeBayar,
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
        ActivityLog::catat('pesanan baru', $pesanan->kode,
            'Total Rp'.number_format($total, 0, ',', '.').' ('.$pesanan->labelMetode().')');

        // COD: langsung dikonfirmasi (stok berkurang), bayar tunai saat kurir tiba.
        if ($metodeBayar === 'cod') {
            $this->pesananService->tandaiLunas($pesanan, 'cod');
        }

        // Notifikasi ke admin
        Notifikasi::keSemuaAdmin(
            'Pesanan baru masuk',
            $pesanan->kode.' senilai Rp'.number_format($total, 0, ',', '.').' — '.$pesanan->labelMetode(),
            route('admin.pesanan.show', $pesanan),
            'pesanan',
        );
        // Notifikasi ke pelanggan
        Notifikasi::kirim($pesanan->user_id, 'Pesanan dibuat',
            $metodeBayar === 'cod'
                ? 'Pesanan '.$pesanan->kode.' (COD) dikonfirmasi. Siapkan uang tunai saat kurir tiba.'
                : 'Pesanan '.$pesanan->kode.' menunggu pembayaran.',
            route('pesanan.show', $pesanan->kode), 'status');

        // Email konfirmasi (kegagalan kirim tidak boleh menggagalkan checkout).
        rescue(fn () => $pesanan->user->notify(new \App\Notifications\PesananDibuat($pesanan)), null, false);

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
