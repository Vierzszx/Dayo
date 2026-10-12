{{--
    Detail penawaran. Variabel: $p. Tombol Pesan hanya aktif jika stok > 0 dan belum lewat batas ambil.
    Field: id, nama, toko_nama, lokasi, deskripsi, harga_normal, harga_diskon, stok, waktu_mulai, batas_pengambilan, foto, no_wa
--}}
@php
    $p = is_array($p) ? (object) $p : $p;
    $namaToko = $p->toko_nama ?? optional($p->toko ?? null)->nama ?? '';
    $foto  = \Illuminate\Support\Str::startsWith($p->foto ?? '', ['http','/']) ? $p->foto : asset('storage/'.($p->foto ?? ''));
    $batas = \Carbon\Carbon::parse($p->batas_pengambilan);
    $tersedia = $p->stok > 0 && now()->lt($batas);
    $penjual = optional(auth()->user())->role === 'penjual';
@endphp
@extends('layouts.dayo')
@section('judul', $p->nama)
@section('konten')
<section class="detail" data-harga="{{ $p->harga_diskon }}" data-stok="{{ $p->stok }}">
    <img class="detail__foto" src="{{ $foto }}" alt="{{ $p->nama }}">
    <div class="detail__info">
        <h1>{{ $p->nama }}</h1>
        <p class="teks-lembut">{{ $p->lokasi ?? $namaToko }}</p>
        <p class="detail__deskripsi">{{ $p->deskripsi ?? '' }}</p>
        <p class="harga harga--besar">
            <s>Rp {{ number_format($p->harga_normal, 0, ',', '.') }}</s>
            <strong>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</strong>
        </p>
        <p class="teks-lembut">Sisa stok <strong>{{ $p->stok }}</strong>, ambil sebelum <strong>{{ $batas->translatedFormat('j M, H:i') }}</strong></p>

        @if ($tersedia)
            <form method="GET" action="{{ route('pesanan.create') }}">
                <input type="hidden" name="penawaran" value="{{ $p->id }}">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" class="input input--kecil" value="1" min="1" max="{{ $p->stok }}" data-input-jumlah>
                <p class="total">Total <strong data-total>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</strong></p>
                <button type="submit" class="btn btn--utama btn--blok">Pesan</button>
            </form>
        @else
            <p class="alert alert--galat">Penawaran ini sudah habis atau melewati batas pengambilan.</p>
        @endif

        @if (!empty($p->no_wa))
            <a href="https://wa.me/{{ $p->no_wa }}?text={{ urlencode('Halo, saya ingin tanya '.$p->nama) }}" target="_blank" rel="noopener" class="btn btn--wa btn--blok">Hubungi Penjual lewat WhatsApp</a>
        @endif

        @if ($penjual)
            <div class="baris-aksi baris-aksi--kiri">
                <a href="{{ route('penawaran.edit', $p->id) }}" class="btn btn--garis">Edit</a>
                <x-tombol-hapus :action="route('penawaran.destroy', $p->id)" pesan="Hapus penawaran ini?" />
            </div>
        @endif
    </div>
</section>
@endsection
