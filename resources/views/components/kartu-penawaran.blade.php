{{--
    Kartu penawaran. Prop $p = model App\Models\Penawaran (relasi produk.toko sudah di-load controller).
    Pemetaan ke backend: nama = produk->nama_produk, toko = produk->toko->nama_toko,
    harga_normal = produk->harga_normal, foto = produk->foto (disk public).
--}}
@props(['p', 'tampilTanggal' => false])
@php
    $namaProduk = $p->produk->nama_produk;
    $namaToko   = $p->produk->toko->nama_toko;
    $foto  = $p->produk->foto ? asset('storage/'.$p->produk->foto) : null;
    // Aturan "bisa dipesan" sama dengan PesananController@create/store
    $tersedia = $p->status === 'aktif' && $p->stok > 0 && $p->waktu_mulai->lte(now()) && $p->batas_pengambilan->gt(now());
@endphp
<article class="kartu" data-cari="{{ mb_strtolower($namaProduk.' '.$namaToko) }}">
    <div class="kartu__gambar {{ $foto ? '' : 'kartu__gambar--kosong' }}">
        @if ($foto) <img src="{{ $foto }}" alt="{{ $namaProduk }}" loading="lazy"> @endif
        <span class="chip">Ambil sebelum {{ $tampilTanggal ? $p->batas_pengambilan->translatedFormat('j M, H:i') : $p->batas_pengambilan->format('H:i') }}</span>
    </div>
    <div class="kartu__isi">
        <h3>{{ $namaProduk }}</h3>
        <p class="teks-lembut">{{ $namaToko }}</p>
        <p class="harga">
            <s>Rp {{ number_format($p->produk->harga_normal, 0, ',', '.') }}</s>
            <strong>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</strong>
        </p>
        <p>Sisa: <strong>{{ $p->stok }}</strong></p>
        <div class="kartu__aksi">
            <a href="{{ route('penawaran.show', $p->penawaran_id) }}" class="btn btn--garis">Detail</a>
            @if ($tersedia)
                <a href="{{ route('pesanan.create', ['penawaran_id' => $p->penawaran_id]) }}" class="btn btn--utama">Pesan</a>
            @else
                <span class="btn btn--utama btn--nonaktif">Tidak tersedia</span>
            @endif
        </div>
    </div>
</article>
