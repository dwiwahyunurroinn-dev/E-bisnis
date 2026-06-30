<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(): View
    {
        $notifikasi = auth()->user()->notifikasi()->latest()->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    /** Buka notifikasi: tandai dibaca lalu arahkan ke tautannya. */
    public function buka(Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === auth()->id(), 403);

        if ($notifikasi->belumDibaca()) {
            $notifikasi->update(['dibaca_at' => now()]);
        }

        return redirect($notifikasi->tautan ?: route('notifikasi.index'));
    }

    public function bacaSemua(): RedirectResponse
    {
        auth()->user()->notifikasi()->belumDibaca()->update(['dibaca_at' => now()]);

        return back()->with('sukses', 'Semua notifikasi ditandai dibaca.');
    }
}
