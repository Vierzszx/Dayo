{{--
    Dashboard Penjual.
    Variabel dari controller:
      $ringkasan = ['masuk'=>int, 'stok'=>int, 'pemasukan'=>int]
      $pesanan   = koleksi pesanan masuk (id, pembeli, item, status)
--}}
@php $ringkasan = is_array($ringkasan) ? (object) $ringkasan : $ringkasan; @endphp
@extends('layouts.dayo')
@section('judul', 'Dashboard Penjual')

@section('konten')
    <h1 class="judul-halaman">Dashboard Penjual</h1>

    <div class="grid">
        <div class="stat"><span>Pesanan masuk</span><strong>{{ $ringkasan->masuk }}</strong></div>
        <div class="stat"><span>Stok tersisa</span><strong>{{ $ringkasan->stok }}</strong></div>
        <div class="stat"><span>Pemasukan</span><strong>Rp {{ number_format($ringkasan->pemasukan, 0, ',', '.') }}</strong></div>
    </div>

    <div class="baris-aksi baris-aksi--kiri">
        <a href="{{ route('toko.index') }}" class="btn btn--garis">Kelola Toko</a>
        <a href="{{ route('produk.index') }}" class="btn btn--garis">Kelola Produk</a>
        <a href="{{ route('penawaran.index') }}" class="btn btn--garis">Kelola Penawaran</a>
    </div>

    <h2 class="judul-seksi">Pesanan masuk</h2>
    <div class="tabel-bungkus">
        <table class="tabel">
            <thead><tr><th>Pembeli</th><th>Item</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($pesanan as $o)
                @php $o = is_array($o) ? (object) $o : $o; @endphp
                <tr>
                    <td>{{ $o->pembeli }}</td>
                    <td>{{ $o->item }}</td>
                    <td><span class="badge badge--{{ $o->status }}">{{ ucfirst(str_replace('-', ' ', $o->status)) }}</span></td>
                    <td><a href="{{ route('pesanan.show', $o->id) }}" class="tautan-aksi">Ubah status</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="teks-lembut">Belum ada pesanan masuk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
