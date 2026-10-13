{{-- Login. Backend: POST login dengan field email & password; error ditaruh pada key `email`. --}}
@extends('layouts.dayo')
@section('judul', 'Login')
@section('konten')
<div class="sempit">
    <h1 class="judul-halaman">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="panel">
        @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" required autocomplete="email">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="input" required autocomplete="current-password">
        <div class="baris-aksi">
            <button type="submit" class="btn btn--utama btn--lebar">Masuk</button>
        </div>
    </form>
    <p class="teks-tengah teks-lembut">Belum punya akun? <a href="{{ route('register') }}" class="tautan-aksi">Daftar</a></p>
</div>
@endsection
