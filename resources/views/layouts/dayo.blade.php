{{--
    layouts/dayo.blade.php - layout induk Dayo (satu-satunya layout aplikasi).
    Mengikuti backend: kolom pengguna `nama` & `peran` (buyer/seller), flash `success`,
    dan nama rute dari routes/web.php.
--}}
@php
    $user     = auth()->user();
    $isSeller = $user && $user->peran === 'seller';
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
            @auth
                <a href="{{ route('penawaran.index') }}">Penawaran</a>
                <a href="{{ route('pesanan.index') }}">Pesanan</a>
                @if ($isSeller)
                    <a href="{{ route('toko.index') }}">Toko</a>
                    <a href="{{ route('produk.index') }}">Produk</a>
                @endif
            @endauth
        </nav>
        <div class="navbar__user">
            @auth
                <span>{{ $user->nama }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="btn btn--peach">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn--peach">Login</a>
                <a href="{{ route('register') }}" class="btn btn--peach">Daftar</a>
            @endauth
        </div>
    </div>
</header>

<main class="container halaman">
    {{-- Notifikasi dari backend: ->with('success', '...') dan error validasi ($errors) --}}
    @if (session('success')) <div class="alert alert--sukses">{{ session('success') }}</div> @endif
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
