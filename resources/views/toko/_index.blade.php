{{-- Kelola toko (penjual). Variabel: $items (paginator Toko milik penjual). --}}
<div class="judul-baris">
    <h1 class="judul-halaman">Kelola Toko</h1>
    <a href="{{ route('toko.create') }}" class="btn btn--utama">Tambah Toko</a>
</div>
<div class="tabel-bungkus">
    <table class="tabel">
        <thead><tr><th>Nama</th><th>Titik pengambilan</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($items as $t)
            <tr>
                <td>{{ $t->nama_toko }}</td><td>{{ $t->titik_pengambilan }}</td>
                <td><div class="aksi-baris">
                    <a href="{{ route('toko.show', $t->toko_id) }}" class="btn btn--garis btn--kecil">Detail</a>
                    <a href="{{ route('toko.edit', $t->toko_id) }}" class="btn btn--garis btn--kecil">Edit</a>
                    <x-tombol-hapus :action="route('toko.destroy', $t->toko_id)" :pesan="'Hapus toko '.$t->nama_toko.'?'" />
                </div></td>
            </tr>
        @empty
            <tr><td colspan="3" class="teks-lembut">Belum ada toko.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $items->links('vendor.pagination.dayo') }}
