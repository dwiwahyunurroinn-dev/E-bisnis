<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $cred = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($cred, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        if (! Auth::user()->aktif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi admin.',
            ]);
        }

        $request->session()->regenerate();
        ActivityLog::catat('login', 'Auth', Auth::user()->email.' masuk');

        return Auth::user()->isAdmin()
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('produk.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'telepon'  => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'telepon'  => $data['telepon'] ?? null,
            'password' => $data['password'],
            'role'     => 'pelanggan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('produk.index')->with('sukses', 'Selamat datang di Eco Craft, '.$user->name.'!');
    }

    /* ---------------- Lupa / reset kata sandi ---------------- */

    public function showLupaPassword(): View
    {
        return view('auth.lupa-password');
    }

    public function kirimLinkReset(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // Selalu tampilkan pesan yang sama agar email terdaftar tidak bisa ditebak.
        return back()->with('sukses', 'Jika email terdaftar, tautan reset sudah kami kirim. Cek kotak masuk/spam.');
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->update(['password' => $password]);
            ActivityLog::catat('reset password', 'Auth', $user->email);
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')->with('sukses', 'Kata sandi berhasil diubah. Silakan masuk.');
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::catat('logout', 'Auth', optional(Auth::user())->email.' keluar');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('produk.index')->with('sukses', 'Anda telah keluar.');
    }
}
