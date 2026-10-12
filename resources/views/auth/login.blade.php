{{-- Login: email, password, tombol Login, tautan Daftar. POST ke route('login'). --}}
@extends('layouts.dayo')
@section('judul', 'Login')
@section('konten')
<div class="sempit">
    <h1 class="judul-halaman">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="panel">
        @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="input" required>

        <div class="baris-aksi"><button type="submit" class="btn btn--utama btn--lebar">Login</button></div>
        <p class="teks-lembut teks-tengah">Belum punya akun? <a href="{{ route('register') }}" class="tautan-aksi">Daftar</a></p>
    </form>
</div>
@endsection
