@extends('layouts.admin')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
<div class="panel" style="margin-top:0;max-width:560px;">
    <form method="POST" action="{{ $user->exists ? route('admin.user.update', $user) : route('admin.user.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="field">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}">
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="two">
            <div class="field">
                <label>Telepon</label>
                <input type="text" name="telepon" value="{{ old('telepon', $user->telepon) }}">
            </div>
            <div class="field">
                <label>Role</label>
                <select name="role">
                    <option value="pelanggan" {{ old('role', $user->role) === 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </div>
        <div class="field">
            <label>Kata Sandi {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
            <input type="password" name="password">
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        @if ($user->exists)
            <label style="display:flex;align-items:center;gap:8px;font-size:.9rem;margin-bottom:14px;">
                <input type="checkbox" name="aktif" value="1" {{ $user->aktif ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--primary);"> Akun aktif
            </label>
        @endif

        <div style="display:flex;gap:10px;">
            <button class="btn btn-primary"> Simpan</button>
            <a href="{{ route('admin.user.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection
