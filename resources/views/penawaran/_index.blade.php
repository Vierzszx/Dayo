{{--
    Daftar penawaran. Variabel: $items (paginator Penawaran dengan relasi produk.toko).
    Backend sudah menyaring: pembeli hanya melihat penawaran yang stok > 0 dan belum lewat batas;
    penjual hanya melihat penawaran miliknya.
--}}
@if (auth()->user()->peran === 'seller')
    <div class="judul-baris">
        <h1 class="judul-halaman">Kelola Penawaran</h1>
        <a href="{{ route('penawaran.create') }}" class="btn btn--utama">Tambah Penawaran</a>
    </div>
    <div class="tabel-bungkus">
        <table class="tabel">
            <thead><tr><th>Produk</th><th>Toko</th><th>Harga diskon</th><th>Stok</th><th>Status</th><th>Batas ambil</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($items as $p)
                <tr>
                    <td>{{ $p->produk->nama_produk }}</td>
                    <td>{{ $p->produk->toko->nama_toko }}</td>
                    <td>Rp {{ number_format($p->harga_diskon, 0, ',', '.') }}</td>
                    <td>{{ $p->stok }}</td>
                    <td><span class="badge badge--{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td>{{ $p->batas_pengambilan->translatedFormat('j M, H:i') }}</td>
                    <td><div class="aksi-baris">
                        <a href="{{ route('penawaran.show', $p->penawaran_id) }}" class="btn btn--garis btn--kecil">Detail</a>
                        <a href="{{ route('penawaran.edit', $p->penawaran_id) }}" class="btn btn--garis btn--kecil">Edit</a>
                        <x-tombol-hapus :action="route('penawaran.destroy', $p->penawaran_id)" pesan="Hapus penawaran ini?" />
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="teks-lembut">Belum ada penawaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@else
    <p class="teks-lembut teks-tebal">Sisa danusan hari ini</p>
    <h1 class="hero">Makanan enak, harga lebih hemat, dana kegiatan tetap jalan</h1>

    {{-- Pencarian di sisi klien oleh dayo.js (menyaring kartu pada halaman ini) --}}
    <div class="cari">
        <input type="search" id="kolom-cari" class="input" placeholder="Cari makanan atau toko" autocomplete="off">
        <button type="button" class="btn btn--utama" id="tombol-cari">Cari</button>
    </div>

    <h2 class="judul-seksi">Tersedia sekarang</h2>
    <div class="grid" id="grid-penawaran">
        @foreach ($items as $p)
            <x-kartu-penawaran :p="$p" :tampilTanggal="true" />
        @endforeach
    </div>
    <p id="kosong" class="teks-lembut" @if ($items->count() > 0) hidden @endif>
        {{ $items->count() > 0 ? 'Tidak ada penawaran yang cocok.' : 'Belum ada penawaran tersedia.' }}
    </p>
@endif
{{ $items->links('vendor.pagination.dayo') }}
