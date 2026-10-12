{{--
    Daftar pesanan. Backend (PesananController@index): $items (paginator Pesanan dengan penawaran.produk.toko & pembeli).
    Pembeli melihat pesanannya sendiri; penjual melihat pesanan untuk tokonya.
--}}
@extends('layouts.dayo')
@section('judul', 'Pesanan')
@section('konten')
@php
    $penjual = auth()->user()->peran === 'seller';
    $label = ['pending' => 'Menunggu', 'siap_diambil' => 'Siap diambil', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan', 'kadaluwarsa' => 'Kedaluwarsa'];
@endphp
<div class="judul-baris">
    <h1 class="judul-halaman">{{ $penjual ? 'Pesanan Masuk' : 'Pesanan Saya' }}</h1>
    @if (! $penjual)
        <a href="{{ route('pesanan.create') }}" class="btn btn--utama">Buat Pesanan</a>
    @endif
</div>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>No.</th><th>Produk</th><th>Toko</th>@if ($penjual)<th>Pembeli</th>@endif<th>Jumlah</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($items as $o)
            <tr>
                <td>#{{ $o->pesanan_id }}</td>
                <td>{{ $o->penawaran->produk->nama_produk }}</td>
                <td>{{ $o->penawaran->produk->toko->nama_toko }}</td>
                @if ($penjual)<td>{{ $o->pembeli->nama }}</td>@endif
                <td>{{ $o->jumlah }}</td>
                <td>Rp {{ number_format($o->total_harga, 0, ',', '.') }}</td>
                <td><span class="badge badge--{{ $o->status }}">{{ $label[$o->status] ?? $o->status }}</span></td>
                <td><div class="aksi-baris">
                    <a href="{{ route('pesanan.show', $o->pesanan_id) }}" class="btn btn--garis btn--kecil">Detail</a>
                    @if (in_array($o->status, ['pending', 'siap_diambil'], true))
                        <a href="{{ route('pesanan.edit', $o->pesanan_id) }}" class="btn btn--garis btn--kecil">Kelola</a>
                    @endif
                    @if ($o->status === 'dibatalkan')
                        <x-tombol-hapus :action="route('pesanan.destroy', $o->pesanan_id)" pesan="Hapus pesanan dibatalkan ini?" />
                    @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="{{ $penjual ? 8 : 7 }}" class="teks-lembut">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $items->links('vendor.pagination.dayo') }}
@endsection
