{{--
    Detail Pesanan. Variabel dari controller: $o (Pesanan).
    Field: id, status, makanan, toko, pembeli, jumlah, harga (satuan), batas.
    Tombol mengirim PATCH ke pesanan.update dengan field "status".
--}}
@php
    $o = is_array($o) ? (object) $o : $o;
    $batas = \Carbon\Carbon::parse($o->batas_pengambilan);
    $penjual = optional(auth()->user())->role === 'penjual';
@endphp
@extends('layouts.dayo')
@section('judul', 'Pesanan #'.$o->id)

@section('konten')
<div class="sempit">
    <a href="{{ Route::has('penjual.dashboard') ? route('penjual.dashboard') : route('pesanan.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">
        Pesanan #{{ $o->id }}
        <span class="badge badge--{{ $o->status }}">{{ ucfirst(str_replace('-', ' ', $o->status)) }}</span>
    </h1>

    <table class="tabel-rincian">
        <tr><th>Makanan</th><td>{{ $o->makanan }}</td></tr>
        <tr><th>Toko</th><td>{{ $o->toko }}</td></tr>
        <tr><th>Pembeli</th><td>{{ $o->pembeli }}</td></tr>
        <tr><th>Jumlah</th><td>{{ $o->jumlah }}</td></tr>
        <tr><th>Harga satuan</th><td>Rp {{ number_format($o->harga, 0, ',', '.') }}</td></tr>
        <tr><th>Total</th><td class="total"><strong>Rp {{ number_format($o->jumlah * $o->harga, 0, ',', '.') }}</strong></td></tr>
        <tr><th>Batas pengambilan</th><td>{{ $batas->translatedFormat('j M Y, H:i') }}</td></tr>
    </table>

    <p class="teks-lembut kecil">Pembayaran dilakukan langsung di toko saat pesanan diambil.</p>

    <div class="baris-aksi baris-aksi--kiri">
        @if ($penjual)
        <form method="POST" action="{{ route('pesanan.update', $o->id) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="selesai">
            <button class="btn btn--utama">Tandai selesai</button>
        </form>
        @endif
        @if ($penjual || $o->status === 'menunggu')
        <form method="POST" action="{{ route('pesanan.update', $o->id) }}"
              data-konfirmasi="Yakin ingin membatalkan pesanan ini?">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="dibatalkan">
            <button class="btn btn--bahaya">Batalkan</button>
        </form>
        @endif
    </div>
</div>
@endsection
