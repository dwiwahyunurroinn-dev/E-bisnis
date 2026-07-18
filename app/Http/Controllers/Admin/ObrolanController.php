<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Obrolan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ObrolanController extends Controller
{
    public function index(): View
    {
        $obrolans = Obrolan::with('user')
            ->orderByRaw("status = 'menunggu_admin' desc")
            ->orderByDesc('terakhir_pesan_at')
            ->paginate(20);

        return view('admin.obrolan.index', compact('obrolans'));
    }

    public function show(Obrolan $obrolan): View
    {
        $obrolan->load(['user', 'pesan' => fn ($q) => $q->orderBy('id')]);

        return view('admin.obrolan.show', compact('obrolan'));
    }

    public function balas(Request $request, Obrolan $obrolan): RedirectResponse
    {
        $data = $request->validate(['pesan' => ['required', 'string', 'max:1000']]);

        $obrolan->pesan()->create(['pengirim' => 'admin', 'pesan' => $data['pesan']]);
        $obrolan->update([
            'admin_id' => auth()->id(),
            'terakhir_pesan_at' => now(),
        ]);

        if ($obrolan->user_id) {
            Notifikasi::kirim($obrolan->user_id, 'Admin membalas live chat Anda',
                Str::limit($data['pesan'], 80), null, 'chat');
        }

        return back()->with('sukses', 'Balasan terkirim.');
    }

    public function selesai(Obrolan $obrolan): RedirectResponse
    {
        $obrolan->update(['status' => 'selesai']);
        ActivityLog::catat('menyelesaikan', 'Obrolan #'.$obrolan->id, $obrolan->namaTampil());

        return redirect()->route('admin.obrolan.index')->with('sukses', 'Percakapan ditandai selesai.');
    }
}
