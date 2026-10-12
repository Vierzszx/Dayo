{{--
    Kartu penawaran (array atau model). Tombol: Detail dan Pesan (Pesan nonaktif jika tidak tersedia).
    Field: id, nama, toko_nama, harga_normal, harga_diskon, stok, batas_pengambilan, foto
--}}
@props(['p', 'tampilTanggal' => false])
@php
    $p = is_array($p) ? (object) $p : $p;
    $namaToko = $p->toko_nama ?? optional($p->toko ?? null)->nama ?? '';
    $foto  = \Illuminate\Support\Str::startsWith($p->foto ?? '', ['http', '/']) ? $p->foto : asset('storage/'.($p->foto ?? ''));
    $batas = \Carbon\Carbon::parse($p->batas_pengambilan);
    $tersedia = $p->stok > 0 && now()->lt($batas);   // masih ada stok & belum lewat batas
@endphp
<article class="kartu" data-cari="{{ strtolower($p->nama.' '.$namaToko) }}">
    <div class="kartu__gambar">
        <img src="{{ $foto }}" alt="{{ $p->nama }}" loading="lazy">
        <span class="chip">Ambil sebelum {{ $tampilTanggal ? $batas->translatedFormat('j M, H:i') : $batas->format('H:i') }}</span>
    </div>
    <div class="kartu__isi">
        <h3>{{ $p->nama }}</h3>
        <p class="teks-lembut">{{ $namaToko }}</p>
        <p class="harga">
            <s>Rp {{ number_format($p->harga_normal, 0, ',', '.') }}</s>
            <strong>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</strong>
        </p>
        <p>Sisa: <strong>{{ $p->stok }}</strong></p>
        <div class="kartu__aksi">
            <a href="{{ route('penawaran.show', $p->id) }}" class="btn btn--garis">Detail</a>
            <a href="{{ route('pesanan.create', ['penawaran' => $p->id]) }}"
               class="btn btn--utama {{ $tersedia ? '' : 'btn--nonaktif' }}">{{ $tersedia ? 'Pesan' : 'Habis' }}</a>
        </div>
    </div>
</article>
