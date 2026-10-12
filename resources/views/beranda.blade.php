{{-- Beranda: hero, kolom pencarian, dan grid penawaran tersedia. --}}
@extends('layouts.dayo')
@section('judul', 'Beranda')

@section('konten')
    <p class="teks-lembut teks-tebal">Sisa danusan hari ini</p>
    <h1 class="hero">Makanan enak, harga lebih hemat, dana kegiatan tetap jalan</h1>

    {{-- Pencarian dilakukan di sisi klien oleh dayo.js (filter kartu berdasarkan teks) --}}
    <div class="cari">
        <input type="search" id="kolom-cari" placeholder="Cari makanan atau toko" autocomplete="off">
        <button type="button" class="btn btn--utama" id="tombol-cari">Cari</button>
    </div>

    <h2 class="judul-seksi">Tersedia sekarang</h2>
    <div class="grid" id="grid-penawaran">
        @foreach ($daftar as $p)
            <x-kartu-penawaran :p="$p" tombol="Lihat dan Pesan" />
        @endforeach
    </div>
    <p id="kosong" class="teks-lembut" hidden>Tidak ada penawaran yang cocok.</p>
@endsection

@section('footer')
    <footer class="footer">Dayo, selamatkan makanan dan dukung kegiatan kampus</footer>
@endsection
