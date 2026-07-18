<?php

namespace App\Services;

use App\Models\Faq;

/**
 * Chatbot FAQ sederhana: mencocokkan pesan pelanggan dengan kata kunci FAQ.
 * Jika tidak ada kecocokan yang cukup kuat, percakapan diteruskan ke admin.
 */
class ChatbotService
{
    /** Skor minimum kata kunci yang cocok agar jawaban bot dianggap valid. */
    private const AMBANG_SKOR = 1;

    public function jawab(string $pesan): ?Faq
    {
        $teks = mb_strtolower($pesan);

        $terbaik = null;
        $skorTerbaik = 0;

        foreach (Faq::aktif()->get() as $faq) {
            $skor = $faq->daftarKataKunci()
                ->filter(fn (string $kataKunci) => $kataKunci !== '' && str_contains($teks, $kataKunci))
                ->count();

            if ($skor > $skorTerbaik) {
                $skorTerbaik = $skor;
                $terbaik = $faq;
            }
        }

        return $skorTerbaik >= self::AMBANG_SKOR ? $terbaik : null;
    }
}
