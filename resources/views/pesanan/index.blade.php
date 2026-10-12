{{-- Daftar pesanan. Pembeli: "Pesanan Saya". Penjual: pesanan masuk. Variabel: $pesanan --}}
@extends('layouts.dayo')
@section('judul', 'Pesanan')
@section('konten')
@php $penjual = optional(auth()->user())->role === 'penjual'; @endphp
<h1 class="judul-halaman">{{ $penjual ? 'Pesanan Masuk' : 'Pesanan Saya' }}</h1>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>#</th><th>Makanan</th>@if($penjual)<th>Pembeli</th>@else<th>Toko</th>@endif<th>Jumlah</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($pesanan as $o)
            <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->makanan }}</td>
                <td>{{ $penjual ? $o->pembeli : $o->toko }}</td>
                <td>{{ $o->jumlah }}</td>
                <td>Rp {{ number_format($o->jumlah * $o->harga, 0, ',', '.') }}</td>
                <td><span class="badge badge--{{ $o->status }}">{{ ucfirst(str_replace('-', ' ', $o->status)) }}</span></td>
                <td><a href="{{ route('pesanan.show', $o->id) }}" class="tautan-aksi">{{ $penjual ? 'Ubah status' : 'Detail' }}</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="teks-lembut">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
