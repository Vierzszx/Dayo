{{-- Kelola toko (penjual): daftar toko. Variabel: $daftar --}}
@extends('layouts.dayo')
@section('judul', 'Kelola Toko')
@section('konten')
<div class="judul-baris">
    <h1 class="judul-halaman">Kelola Toko</h1>
    <a href="{{ route('toko.create') }}" class="btn btn--utama">Tambah Toko</a>
</div>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>Nama</th><th>Alamat</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($daftar as $t)
            <tr>
                <td>{{ $t->nama }}</td><td>{{ $t->alamat ?? '-' }}</td>
                <td><div class="aksi-baris">
                    <a href="{{ route('toko.show', $t->id) }}" class="btn btn--garis btn--kecil">Detail</a>
                    <a href="{{ route('toko.edit', $t->id) }}" class="btn btn--garis btn--kecil">Edit</a>
                    <x-tombol-hapus :action="route('toko.destroy', $t->id)" pesan="Hapus toko {{ $t->nama }}?" />
                </div></td>
            </tr>
        @empty
            <tr><td colspan="3" class="teks-lembut">Belum ada toko.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
