<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.user.form', ['user' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'telepon'  => ['nullable', 'string', 'max:25'],
            'role'     => ['required', 'in:admin,pelanggan'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create($data);
        ActivityLog::catat('membuat', 'User #'.$user->id, $user->email.' ('.$user->role.')');

        return redirect()->route('admin.user.index')->with('sukses', 'Pengguna ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.user.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telepon'  => ['nullable', 'string', 'max:25'],
            'role'     => ['required', 'in:admin,pelanggan'],
            'aktif'    => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data['aktif'] = $request->boolean('aktif');
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        ActivityLog::catat('mengubah', 'User #'.$user->id, $user->email);

        return redirect()->route('admin.user.index')->with('sukses', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $email = $user->email;
        $user->delete();
        ActivityLog::catat('menghapus', 'User', $email);

        return back()->with('sukses', 'Pengguna dihapus.');
    }
}
