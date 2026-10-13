{{--
    Detail penawaran. Variabel: $item (Penawaran). Data produk/toko lewat relasi:
    nama = produk->nama_produk, harga normal & foto & deskripsi = produk, lokasi = toko->titik_pengambilan,
    WhatsApp = toko->nomor_whatsapp.
--}}
@php
    $produk = $item->produk;
    $toko   = $produk->toko;
    $foto   = $produk->foto ? asset('storage/'.$produk->foto) : null;
    $penjual = auth()->user()->peran === 'seller';
    // Aturan "bisa dipesan" sama dengan PesananController@create/store
    $tersedia = $item->status === 'aktif' && $item->stok > 0 && $item->waktu_mulai->lte(now()) && $item->batas_pengambilan->gt(now());
    // Nomor WA bebas diketik penjual; wa.me butuh format internasional tanpa 0/+/spasi
    $wa = preg_replace('/\D+/', '', (string) $toko->nomor_whatsapp);
    if ($wa !== '' && str_starts_with($wa, '0')) { $wa = '62'.substr($wa, 1); }
@endphp
<section class="detail" data-harga="{{ $item->harga_diskon }}" data-stok="{{ $item->stok }}">
    @if ($foto)
        <img class="detail__foto" src="{{ $foto }}" alt="{{ $produk->nama_produk }}">
    @else
        <div class="detail__foto kartu__gambar--kosong" style="min-height:240px"></div>
    @endif
    <div class="detail__info">
        <h1>{{ $produk->nama_produk }}</h1>
        <p class="teks-lembut">{{ $toko->nama_toko }} - {{ $toko->titik_pengambilan }}</p>
        <p class="detail__deskripsi">{{ $produk->deskripsi }}</p>
        <p class="harga harga--besar">
            <s>Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</s>
            <strong>Rp {{ number_format($item->harga_diskon, 0, ',', '.') }}</strong>
        </p>
        <p class="teks-lembut">
            Sisa stok <strong>{{ $item->stok }}</strong>, ambil
            <strong>{{ $item->waktu_mulai->translatedFormat('j M, H:i') }}</strong> sampai
            <strong>{{ $item->batas_pengambilan->translatedFormat('j M, H:i') }}</strong>
            @if ($penjual) · Status: <span class="badge badge--{{ $item->status }}">{{ ucfirst($item->status) }}</span> @endif
        </p>

        @if (! $penjual)
            @if ($tersedia)
                <form method="GET" action="{{ route('pesanan.create') }}">
                    <input type="hidden" name="penawaran_id" value="{{ $item->penawaran_id }}">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" class="input input--kecil" value="1" min="1" max="{{ $item->stok }}" data-input-jumlah>
                    <p class="total">Total <strong data-total>Rp {{ number_format($item->harga_diskon, 0, ',', '.') }}</strong></p>
                    <button type="submit" class="btn btn--utama btn--blok">Pesan</button>
                </form>
            @else
                <p class="alert alert--galat">Penawaran ini belum dimulai, habis, atau sudah melewati batas pengambilan.</p>
            @endif
        @endif

        @if ($wa !== '')
            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya ingin tanya '.$produk->nama_produk) }}" target="_blank" rel="noopener" class="btn btn--wa btn--blok">Hubungi Penjual lewat WhatsApp</a>
        @endif

        <a href="{{ route('penawaran.index') }}" class="tautan-kembali" style="display:inline-block;margin-top:1rem">Kembali ke daftar</a>

        @if ($penjual)
            <div class="baris-aksi baris-aksi--kiri">
                <a href="{{ route('penawaran.edit', $item->penawaran_id) }}" class="btn btn--garis">Edit</a>
                <x-tombol-hapus :action="route('penawaran.destroy', $item->penawaran_id)" pesan="Hapus penawaran ini?" />
            </div>
        @endif
    </div>
</section>
