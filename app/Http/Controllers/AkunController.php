<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        return view('akun.dashboard', [
            'user'          => $user,
            'totalPesanan'  => $user->pesanan()->count(),
            'perluBayar'    => $user->pesanan()->where('status', 'pending')->count(),
            'sedangProses'  => $user->pesanan()->whereIn('status', ['lunas', 'diproses', 'dikirim'])->count(),
            'selesai'       => $user->pesanan()->where('status', 'selesai')->count(),
            'pesananTerbaru'=> $user->pesanan()->with('detail')->latest()->take(5)->get(),
        ]);
    }

    public function profil(): View
    {
        return view('akun.profil', ['user' => auth()->user()]);
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:25'],
        ]);

        $user->update($data);

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama' => ['required'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($request->password_lama, $request->user()->password)) {
            return back()->withErrors(['password_lama' => 'Kata sandi lama salah.']);
        }

        $request->user()->update(['password' => $request->password]);

        return back()->with('sukses', 'Kata sandi berhasil diubah.');
    }

    public function pesanan(): View
    {
        $pesanan = auth()->user()->pesanan()->with('detail')->latest()->paginate(10);

        return view('akun.pesanan', compact('pesanan'));
    }
}
