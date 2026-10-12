{{-- Detail toko. Variabel: $toko (nama, alamat, no_wa, deskripsi) --}}
@extends('layouts.dayo')
@section('judul', $toko->nama)
@section('konten')
<div class="sempit">
    <a href="{{ route('toko.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">{{ $toko->nama }}</h1>
    <table class="tabel-rincian">
        <tr><th>Alamat</th><td>{{ $toko->alamat ?? '-' }}</td></tr>
        <tr><th>No. WhatsApp</th><td>{{ $toko->no_wa ?? '-' }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $toko->deskripsi ?? '-' }}</td></tr>
    </table>
    <div class="baris-aksi baris-aksi--kiri">
        <a href="{{ route('toko.edit', $toko->id) }}" class="btn btn--utama">Edit</a>
        <x-tombol-hapus :action="route('toko.destroy', $toko->id)" pesan="Hapus toko ini?" />
    </div>
</div>
@endsection
