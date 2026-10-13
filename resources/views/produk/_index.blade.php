{{-- Kelola produk (penjual). Variabel: $items (paginator Produk milik penjual). --}}
<div class="judul-baris">
    <h1 class="judul-halaman">Kelola Produk</h1>
    <a href="{{ route('produk.create') }}" class="btn btn--utama">Tambah Produk</a>
</div>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>Foto</th><th>Nama</th><th>Toko</th><th>Harga normal</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($items as $pr)
            <tr>
                <td>
                    @if ($pr->foto) <img class="thumb" src="{{ asset('storage/'.$pr->foto) }}" alt="{{ $pr->nama_produk }}">
                    @else <div class="thumb thumb--kosong"></div> @endif
                </td>
                <td>{{ $pr->nama_produk }}</td>
                <td>{{ $pr->toko->nama_toko }}</td>
                <td>Rp {{ number_format($pr->harga_normal, 0, ',', '.') }}</td>
                <td><div class="aksi-baris">
                    <a href="{{ route('produk.show', $pr->produk_id) }}" class="btn btn--garis btn--kecil">Detail</a>
                    <a href="{{ route('produk.edit', $pr->produk_id) }}" class="btn btn--garis btn--kecil">Edit</a>
                    <x-tombol-hapus :action="route('produk.destroy', $pr->produk_id)" :pesan="'Hapus produk '.$pr->nama_produk.'?'" />
                </div></td>
            </tr>
        @empty
            <tr><td colspan="5" class="teks-lembut">Belum ada produk.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $items->links('vendor.pagination.dayo') }}
