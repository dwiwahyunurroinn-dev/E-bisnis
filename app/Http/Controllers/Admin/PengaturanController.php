<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        return view('admin.pengaturan.index', ['nilai' => Pengaturan::semua()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'          => ['required', 'string', 'max:80'],
            'tagline'       => ['nullable', 'string', 'max:120'],
            'email'         => ['nullable', 'email', 'max:150'],
            'telepon'       => ['nullable', 'string', 'max:30'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
            'whatsapp_text' => ['nullable', 'string', 'max:200'],
            'instagram'     => ['nullable', 'string', 'max:60'],
            'rekening'      => ['nullable', 'string', 'max:1000'],
            'ewallet_gopay'     => ['nullable', 'string', 'max:100'],
            'ewallet_ovo'       => ['nullable', 'string', 'max:100'],
            'ewallet_dana'      => ['nullable', 'string', 'max:100'],
            'ewallet_shopeepay' => ['nullable', 'string', 'max:100'],
            'logo'          => ['nullable', 'image', 'max:2048'],
            'qris_gambar'   => ['nullable', 'image', 'max:2048'],
        ]);

        // Normalisasi: @handle -> handle; nomor WA -> format internasional 62xxx.
        $data['instagram'] = ltrim((string) ($data['instagram'] ?? ''), '@') ?: null;
        if (! empty($data['whatsapp'])) {
            $wa = preg_replace('/\D/', '', $data['whatsapp']);
            $data['whatsapp'] = str_starts_with($wa, '0') ? '62'.substr($wa, 1) : $wa;
        }

        foreach (['nama', 'tagline', 'email', 'telepon', 'whatsapp', 'whatsapp_text', 'instagram', 'rekening',
            'ewallet_gopay', 'ewallet_ovo', 'ewallet_dana', 'ewallet_shopeepay'] as $k) {
            Pengaturan::simpan($k, $data[$k] ?? null);
        }

        $this->simpanGambar($request, 'logo', 'hapus_logo');
        $this->simpanGambar($request, 'qris_gambar', 'hapus_qris');

        ActivityLog::catat('mengubah', 'Pengaturan toko');

        return back()->with('sukses', 'Pengaturan toko disimpan.');
    }

    /** Simpan file upload (atau hapus bila diminta), lalu catat path-nya. */
    private function simpanGambar(Request $request, string $kunci, string $flagHapus): void
    {
        $lama = Pengaturan::ambil($kunci);

        if ($request->boolean($flagHapus)) {
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            Pengaturan::simpan($kunci, null);

            return;
        }

        if ($request->hasFile($kunci)) {
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            Pengaturan::simpan($kunci, $request->file($kunci)->store('pengaturan', 'public'));
        }
    }
}
