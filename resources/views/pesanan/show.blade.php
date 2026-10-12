@extends('layouts.app')

@section('content')
    <h1>Pesanan #{{ $item->pesanan_id }}</h1>

    <p>Produk: {{ $item->penawaran->produk->nama_produk }}</p>

    <p>Toko: {{ $item->penawaran->produk->toko->nama_toko }}</p>

    <p>
        Titik pengambilan:
        {{ $item->penawaran->produk->toko->titik_pengambilan }}
    </p>

    <p>
        WhatsApp toko:
        {{ $item->penawaran->produk->toko->nomor_whatsapp ?: '-' }}
    </p>

    <p>Pembeli: {{ $item->pembeli->nama }}</p>

    <p>Jumlah: {{ $item->jumlah }}</p>

    <p>
        Total: Rp{{ number_format($item->total_harga, 2, ',', '.') }}
    </p>

    <p>Status: {{ $item->status }}</p>

    <p>Kode pengambilan: {{ $item->kode_pengambilan }}</p>

    <p>
        Pengambilan:
        {{ $item->penawaran->waktu_mulai->format('d/m/Y H:i') }}
        sampai
        {{ $item->penawaran->batas_pengambilan->format('d/m/Y H:i') }}
    </p>

    @if($item->waktu_diambil)
        <p>
            Diambil pada:
            {{ $item->waktu_diambil->format('d/m/Y H:i') }}
        </p>
    @endif

    <p>Pembayaran dilakukan saat pengambilan.</p>

    <a href="{{ route('pesanan.index') }}">Kembali</a>
@endsection