{{-- Daftar. Backend: nama, email, password, password_confirmation, peran (buyer|seller). --}}
@extends('layouts.dayo')
@section('judul', 'Daftar')
@section('konten')
<div class="sempit">
    <h1 class="judul-halaman">Daftar akun</h1>
    <form method="POST" action="{{ route('register') }}" class="panel">
        @csrf
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" class="input" value="{{ old('nama') }}" required maxlength="100" autocomplete="name">

        <label>Daftar sebagai</label>
        <div class="pilihan">
            <label><input type="radio" name="peran" value="buyer" @checked(old('peran', 'buyer') === 'buyer')> Pembeli</label>
            <label><input type="radio" name="peran" value="seller" @checked(old('peran') === 'seller')> Penjual</label>
        </div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" required autocomplete="email">
        <label for="password">Password (minimal 8 karakter)</label>
        <input type="password" id="password" name="password" class="input" required minlength="8" autocomplete="new-password">
        <label for="password_confirmation">Konfirmasi password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="input" required minlength="8" autocomplete="new-password">
        <div class="baris-aksi">
            <button type="submit" class="btn btn--utama btn--lebar">Daftar</button>
        </div>
    </form>
    <p class="teks-tengah teks-lembut">Sudah punya akun? <a href="{{ route('login') }}" class="tautan-aksi">Login</a></p>
</div>
@endsection
