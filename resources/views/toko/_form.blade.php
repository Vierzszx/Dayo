{{-- Form toko. Variabel: $item (Toko|null). Field & aturan mengikuti TokoController. PK: toko_id. --}}
<form method="POST" action="{{ $item ? route('toko.update', $item->toko_id) : route('toko.store') }}" class="panel">
    @csrf @if ($item) @method('PUT') @endif
    <label for="nama_toko">Nama toko</label>
    <input type="text" id="nama_toko" name="nama_toko" class="input" maxlength="255" value="{{ old('nama_toko', $item?->nama_toko) }}" required>
    <label for="titik_pengambilan">Titik pengambilan</label>
    <input type="text" id="titik_pengambilan" name="titik_pengambilan" class="input" maxlength="255" placeholder="Contoh: Gedung A lantai 1" value="{{ old('titik_pengambilan', $item?->titik_pengambilan) }}" required>
    <label for="nomor_whatsapp">No. WhatsApp (opsional)</label>
    <input type="text" id="nomor_whatsapp" name="nomor_whatsapp" class="input" maxlength="20" placeholder="628123456789" value="{{ old('nomor_whatsapp', $item?->nomor_whatsapp) }}">
    <label for="deskripsi">Deskripsi (opsional)</label>
    <textarea id="deskripsi" name="deskripsi" class="input">{{ old('deskripsi', $item?->deskripsi) }}</textarea>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
        <a href="{{ route('toko.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
