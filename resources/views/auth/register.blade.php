{{-- Daftar akun: nama, email, password, konfirmasi password, pilihan Pembeli/Penjual. POST ke route('register'). --}}
@extends('layouts.dayo')
@section('judul', 'Daftar akun')
@section('konten')
<div class="sempit">
    <h1 class="judul-halaman">Daftar akun</h1>
    <form method="POST" action="{{ route('register') }}" class="panel">
        @csrf
        <label for="name">Nama</label>
        <input type="text" id="name" name="name" class="input" value="{{ old('name') }}" required autofocus>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="input" required minlength="8">

        <label for="password_confirmation">Konfirmasi password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="input" required>

        <label>Daftar sebagai</label>
        <div class="pilihan">
            <label><input type="radio" name="role" value="pembeli" @checked(old('role', 'pembeli') === 'pembeli')> Pembeli</label>
            <label><input type="radio" name="role" value="penjual" @checked(old('role') === 'penjual')> Penjual</label>
        </div>

        <div class="baris-aksi"><button type="submit" class="btn btn--utama btn--lebar">Daftar</button></div>
        <p class="teks-lembut teks-tengah">Sudah punya akun? <a href="{{ route('login') }}" class="tautan-aksi">Login</a></p>
    </form>
</div>
@endsection
