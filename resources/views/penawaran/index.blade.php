{{--
    Daftar penawaran. Pembeli: grid kartu (Detail & Pesan). Penjual: tabel kelola (Tambah/Edit/Hapus).
    Variabel: $daftar
--}}
@extends('layouts.dayo')
@section('judul', 'Penawaran')
@section('konten')
@php $penjual = optional(auth()->user())->role === 'penjual'; @endphp

@if ($penjual)
    <div class="judul-baris">
        <h1 class="judul-halaman">Kelola Penawaran</h1>
        <a href="{{ route('penawaran.create') }}" class="btn btn--utama">Tambah Penawaran</a>
    </div>
    <div class="tabel-bungkus">
        <table class="tabel">
            <thead><tr><th>Produk</th><th>Harga diskon</th><th>Stok</th><th>Batas ambil</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($daftar as $p)
                <tr>
                    <td>{{ $p->nama ?? optional($p->produk ?? null)->nama }}</td>
                    <td>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</td>
                    <td>{{ $p->stok }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->batas_pengambilan)->translatedFormat('j M, H:i') }}</td>
                    <td><div class="aksi-baris">
                        <a href="{{ route('penawaran.show', $p->id) }}" class="btn btn--garis btn--kecil">Detail</a>
                        <a href="{{ route('penawaran.edit', $p->id) }}" class="btn btn--garis btn--kecil">Edit</a>
                        <x-tombol-hapus :action="route('penawaran.destroy', $p->id)" pesan="Hapus penawaran ini?" />
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="teks-lembut">Belum ada penawaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@else
    <h1 class="judul-halaman">Daftar Penawaran</h1>
    <div class="grid">
        @forelse ($daftar as $p)
            <x-kartu-penawaran :p="$p" :tampilTanggal="true" />
        @empty
            <p class="teks-lembut">Belum ada penawaran tersedia.</p>
        @endforelse
    </div>
@endif
@endsection
