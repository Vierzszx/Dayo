{{-- Detail produk. Variabel: $produk (nama, deskripsi, harga, foto, toko) --}}
@php $foto = \Illuminate\Support\Str::startsWith($produk->foto ?? '', ['http','/']) ? $produk->foto : asset('storage/'.($produk->foto ?? '')); @endphp
@extends('layouts.dayo')
@section('judul', $produk->nama)
@section('konten')
<div class="sempit">
    <a href="{{ route('produk.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">{{ $produk->nama }}</h1>
    <img src="{{ $foto }}" alt="{{ $produk->nama }}" class="preview">
    <table class="tabel-rincian">
        <tr><th>Toko</th><td>{{ $produk->toko_nama ?? optional($produk->toko ?? null)->nama }}</td></tr>
        <tr><th>Harga normal</th><td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $produk->deskripsi ?? '-' }}</td></tr>
    </table>
    <div class="baris-aksi baris-aksi--kiri">
        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn--utama">Edit</a>
        <x-tombol-hapus :action="route('produk.destroy', $produk->id)" pesan="Hapus produk ini?" />
    </div>
</div>
@endsection
