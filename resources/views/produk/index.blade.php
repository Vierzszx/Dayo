{{-- Kelola produk (penjual). Variabel: $daftar (produk dengan relasi toko) --}}
@extends('layouts.dayo')
@section('judul', 'Kelola Produk')
@section('konten')
<div class="judul-baris">
    <h1 class="judul-halaman">Kelola Produk</h1>
    <a href="{{ route('produk.create') }}" class="btn btn--utama">Tambah Produk</a>
</div>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>Foto</th><th>Nama</th><th>Toko</th><th>Harga</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($daftar as $pr)
            @php $foto = \Illuminate\Support\Str::startsWith($pr->foto ?? '', ['http','/']) ? $pr->foto : asset('storage/'.($pr->foto ?? '')); @endphp
            <tr>
                <td><img class="thumb" src="{{ $foto }}" alt="{{ $pr->nama }}"></td>
                <td>{{ $pr->nama }}</td>
                <td>{{ $pr->toko_nama ?? optional($pr->toko ?? null)->nama }}</td>
                <td>Rp {{ number_format($pr->harga, 0, ',', '.') }}</td>
                <td><div class="aksi-baris">
                    <a href="{{ route('produk.show', $pr->id) }}" class="btn btn--garis btn--kecil">Detail</a>
                    <a href="{{ route('produk.edit', $pr->id) }}" class="btn btn--garis btn--kecil">Edit</a>
                    <x-tombol-hapus :action="route('produk.destroy', $pr->id)" pesan="Hapus produk {{ $pr->nama }}?" />
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5" class="teks-lembut">Belum ada produk.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
