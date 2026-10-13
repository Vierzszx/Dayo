{{-- Detail pesanan. Backend (PesananController@show): $item (Pesanan dengan penawaran.produk.toko & pembeli). --}}
@extends('layouts.dayo')
@section('judul', 'Pesanan #'.$item->pesanan_id)
@section('konten')
@php
    $penawaran = $item->penawaran;
    $toko = $penawaran->produk->toko;
    $label = ['pending' => 'Menunggu', 'siap_diambil' => 'Siap diambil', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan', 'kadaluwarsa' => 'Kedaluwarsa'];
    $wa = preg_replace('/\D+/', '', (string) $toko->nomor_whatsapp);
    if ($wa !== '' && str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
@endphp
<div class="sempit">
    <a href="{{ route('pesanan.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">Pesanan #{{ $item->pesanan_id }}</h1>
    <table class="tabel-rincian">
        <tr><th>Produk</th><td>{{ $penawaran->produk->nama_produk }}</td></tr>
        <tr><th>Toko</th><td>{{ $toko->nama_toko }}</td></tr>
        <tr><th>Titik pengambilan</th><td>{{ $toko->titik_pengambilan }}</td></tr>
        <tr><th>Pembeli</th><td>{{ $item->pembeli->nama }}</td></tr>
        <tr><th>Jumlah</th><td>{{ $item->jumlah }}</td></tr>
        <tr><th>Total</th><td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td></tr>
        <tr><th>Status</th><td><span class="badge badge--{{ $item->status }}">{{ $label[$item->status] ?? $item->status }}</span></td></tr>
        <tr><th>Kode pengambilan</th><td class="teks-kode">{{ $item->kode_pengambilan }}</td></tr>
        <tr><th>Waktu pengambilan</th><td>{{ $penawaran->waktu_mulai->translatedFormat('j M Y, H:i') }} sampai {{ $penawaran->batas_pengambilan->translatedFormat('j M Y, H:i') }}</td></tr>
        @if ($item->waktu_diambil)
            <tr><th>Diambil pada</th><td>{{ $item->waktu_diambil->translatedFormat('j M Y, H:i') }}</td></tr>
        @endif
    </table>
    <p class="teks-lembut">Pembayaran dilakukan saat pengambilan.</p>
    <div class="baris-aksi baris-aksi--kiri">
        @if (in_array($item->status, ['pending', 'siap_diambil'], true))
            <a href="{{ route('pesanan.edit', $item->pesanan_id) }}" class="btn btn--utama">Kelola pesanan</a>
        @endif
        @if ($wa !== '')
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn btn--wa">WhatsApp toko</a>
        @endif
    </div>
</div>
@endsection
