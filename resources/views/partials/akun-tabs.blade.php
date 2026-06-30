@php $r = request()->route()->getName(); @endphp
<div class="akun-tabs">
    <a href="{{ route('akun.dashboard') }}" class="{{ $r === 'akun.dashboard' ? 'active' : '' }}"><x-icon name="dashboard" :size="16"/> Dashboard</a>
    <a href="{{ route('akun.pesanan') }}" class="{{ $r === 'akun.pesanan' ? 'active' : '' }}"><x-icon name="clipboard" :size="16"/> Pesanan</a>
    <a href="{{ route('akun.alamat') }}" class="{{ $r === 'akun.alamat' ? 'active' : '' }}"><x-icon name="pin" :size="16"/> Alamat</a>
    <a href="{{ route('notifikasi.index') }}" class="{{ $r === 'notifikasi.index' ? 'active' : '' }}"><x-icon name="bell" :size="16"/> Notifikasi</a>
    <a href="{{ route('akun.profil') }}" class="{{ $r === 'akun.profil' ? 'active' : '' }}"><x-icon name="user" :size="16"/> Profil</a>
</div>
