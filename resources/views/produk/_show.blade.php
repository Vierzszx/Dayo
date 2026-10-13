{{-- Detail produk. Variabel: $item (Produk). --}}
<div class="sempit">
    <a href="{{ route('produk.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">{{ $item->nama_produk }}</h1>
    @if ($item->foto) <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_produk }}" class="preview"> @endif
    <table class="tabel-rincian">
        <tr><th>Toko</th><td>{{ $item->toko->nama_toko }}</td></tr>
        <tr><th>Harga normal</th><td>Rp {{ number_format($item->harga_normal, 0, ',', '.') }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $item->deskripsi ?: '-' }}</td></tr>
    </table>
    <div class="baris-aksi baris-aksi--kiri">
        <a href="{{ route('produk.edit', $item->produk_id) }}" class="btn btn--utama">Edit</a>
        <x-tombol-hapus :action="route('produk.destroy', $item->produk_id)" pesan="Hapus produk ini?" />
    </div>
</div>
