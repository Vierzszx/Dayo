{{-- Detail toko. Variabel: $item (Toko). --}}
<div class="sempit">
    <a href="{{ route('toko.index') }}" class="tautan-kembali">Kembali ke daftar</a>
    <h1 class="judul-halaman">{{ $item->nama_toko }}</h1>
    <table class="tabel-rincian">
        <tr><th>Titik pengambilan</th><td>{{ $item->titik_pengambilan }}</td></tr>
        <tr><th>No. WhatsApp</th><td>{{ $item->nomor_whatsapp ?: '-' }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $item->deskripsi ?: '-' }}</td></tr>
    </table>
    <div class="baris-aksi baris-aksi--kiri">
        <a href="{{ route('toko.edit', $item->toko_id) }}" class="btn btn--utama">Edit</a>
        <x-tombol-hapus :action="route('toko.destroy', $item->toko_id)" pesan="Hapus toko ini?" />
    </div>
</div>
