{{--
    layouts/dayo.blade.php - layout induk Dayo.
    Navigasi: Penawaran, Pesanan, nama pengguna, Logout; menu Toko & Produk khusus penjual.
    Aman jika rute belum ada (Route::has). Peran dibaca dari auth()->user()->role.
--}}
@php
    $user     = auth()->user();
    $peran    = $peran ?? (optional($user)->role === 'penjual' ? 'penjual' : 'pembeli'); // SESUAIKAN kolom role
    $namaUser = $namaUser ?? optional($user)->name;
    $r = fn ($nama) => Route::has($nama) ? route($nama) : '#';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'Dayo') - Dayo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dayo.css') }}">
</head>
<body>
<header class="navbar">
    <div class="container navbar__isi">
        <a href="{{ url('/') }}" class="navbar__logo">Dayo</a>
        <nav class="navbar__menu">
            <a href="{{ $r('penawaran.index') }}">Penawaran</a>
            <a href="{{ $r('pesanan.index') }}">Pesanan</a>
            @if ($peran === 'penjual')
                <a href="{{ $r('toko.index') }}">Toko</a>
                <a href="{{ $r('produk.index') }}">Produk</a>
            @endif
        </nav>
        <div class="navbar__user">
            @if ($namaUser)
                <span>{{ $namaUser }}</span>
                <form method="POST" action="{{ $r('logout') }}">@csrf
                    <button type="submit" class="btn btn--peach">Logout</button>
                </form>
            @else
                <a href="{{ $r('login') }}" class="btn btn--peach">Login</a>
            @endif
        </div>
    </div>
</header>

<main class="container halaman">
    {{-- Notifikasi: backend cukup memakai ->with('sukses', '...') atau ->with('error', '...') --}}
    @if (session('sukses')) <div class="alert alert--sukses">{{ session('sukses') }}</div> @endif
    @if (session('error'))  <div class="alert alert--galat">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div class="alert alert--galat">Periksa kembali isian Anda:
            <ul>@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif
    @yield('konten')
</main>
@yield('footer')
<script src="{{ asset('js/dayo.js') }}"></script>
@stack('skrip')
</body>
</html>
