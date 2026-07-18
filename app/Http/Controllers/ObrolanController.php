<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Obrolan;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Widget chat publik (storefront): FAQ otomatis via bot, dengan handoff ke admin
 * saat bot tidak punya jawaban atau pelanggan minta bicara dengan manusia.
 */
class ObrolanController extends Controller
{
    private const KEY_TOKEN = 'obrolan_token';

    public function muat(): JsonResponse
    {
        $obrolan = $this->obrolanAktif();

        return $this->respon($obrolan);
    }

    public function kirim(Request $request, ChatbotService $bot): JsonResponse
    {
        $data = $request->validate(['pesan' => ['required', 'string', 'max:500']]);

        $obrolan = $this->obrolanAktif();
        $obrolan->pesan()->create(['pengirim' => 'pelanggan', 'pesan' => $data['pesan']]);

        if ($obrolan->status === 'bot') {
            $faq = $bot->jawab($data['pesan']);

            if ($faq) {
                $obrolan->pesan()->create(['pengirim' => 'bot', 'pesan' => $faq->jawaban]);
            } else {
                $this->teruskanKeAdmin($obrolan);
            }
        }

        $obrolan->update(['terakhir_pesan_at' => now()]);

        return $this->respon($obrolan);
    }

    /** Pelanggan minta bicara langsung dengan admin (lewati bot). */
    public function mintaAdmin(): JsonResponse
    {
        $obrolan = $this->obrolanAktif();

        if ($obrolan->status === 'bot') {
            $this->teruskanKeAdmin($obrolan, otomatis: false);
        }

        return $this->respon($obrolan);
    }

    private function teruskanKeAdmin(Obrolan $obrolan, bool $otomatis = true): void
    {
        $obrolan->update(['status' => 'menunggu_admin']);
        $obrolan->pesan()->create([
            'pengirim' => 'bot',
            'pesan' => $otomatis
                ? 'Maaf, saya belum punya jawaban untuk itu. Pertanyaan Anda sudah diteruskan ke admin kami, mohon tunggu sebentar ya.'
                : 'Baik, saya hubungkan Anda dengan admin kami. Mohon tunggu sebentar ya.',
        ]);
        Notifikasi::keSemuaAdmin(
            'Live chat menunggu balasan',
            'Percakapan dari '.$obrolan->namaTampil().' butuh respons admin.',
            route('admin.obrolan.show', $obrolan),
            'chat',
        );
    }

    private function respon(Obrolan $obrolan): JsonResponse
    {
        return response()->json([
            'obrolan_id' => $obrolan->id,
            'status' => $obrolan->status,
            'pesan' => $obrolan->pesan()->orderBy('id')->get()->map(fn ($p) => [
                'id' => $p->id,
                'pengirim' => $p->pengirim,
                'pesan' => $p->pesan,
                'waktu' => $p->created_at->format('H:i'),
            ]),
        ]);
    }

    /** Ambil sesi obrolan aktif milik user login, atau tamu via token session. */
    private function obrolanAktif(): Obrolan
    {
        if (auth()->check()) {
            $obrolan = Obrolan::where('user_id', auth()->id())->aktif()->latest()->first();

            return $obrolan ?: Obrolan::create([
                'user_id' => auth()->id(),
                'nama' => auth()->user()->name,
                'status' => 'bot',
                'terakhir_pesan_at' => now(),
            ]);
        }

        $token = session()->get(self::KEY_TOKEN);
        $obrolan = $token ? Obrolan::where('token_tamu', $token)->aktif()->latest()->first() : null;

        if ($obrolan) {
            return $obrolan;
        }

        $token = Str::random(32);
        session()->put(self::KEY_TOKEN, $token);

        return Obrolan::create([
            'token_tamu' => $token,
            'nama' => 'Tamu',
            'status' => 'bot',
            'terakhir_pesan_at' => now(),
        ]);
    }
}
