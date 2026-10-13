{{--
    Form produk. Variabel: $item (Produk|null), $options['toko_id'] (id => nama_toko, hanya toko milik penjual).
    Field mengikuti ProdukController: toko_id, nama_produk, deskripsi, harga_normal, foto (jpg/png/webp, maks 2 MB).
--}}
@php $fotoLama = $item?->foto ? asset('storage/'.$item->foto) : ''; @endphp
<form method="POST" action="{{ $item ? route('produk.update', $item->produk_id) : route('produk.store') }}" class="panel" enctype="multipart/form-data">
    @csrf @if ($item) @method('PUT') @endif
    <label for="toko_id">Toko</label>
    <select id="toko_id" name="toko_id" class="input" required>
        <option value="">Pilih toko</option>
        @foreach (($options['toko_id'] ?? []) as $id => $nama)
            <option value="{{ $id }}" @selected((string) old('toko_id', $item?->toko_id) === (string) $id)>{{ $nama }}</option>
        @endforeach
    </select>
    @if (empty($options['toko_id']) || count($options['toko_id']) === 0)
        <small class="teks-lembut">Belum ada toko. <a href="{{ route('toko.create') }}" class="tautan-aksi">Buat toko dulu</a>.</small>
    @endif
    <label for="nama_produk">Nama produk</label>
    <input type="text" id="nama_produk" name="nama_produk" class="input" maxlength="255" value="{{ old('nama_produk', $item?->nama_produk) }}" required>
    <label for="harga_normal">Harga normal (Rp)</label>
    <input type="number" id="harga_normal" name="harga_normal" class="input" min="0.01" step="0.01" value="{{ old('harga_normal', $item?->harga_normal) }}" required>
    <label for="deskripsi">Deskripsi (opsional)</label>
    <textarea id="deskripsi" name="deskripsi" class="input" maxlength="2000">{{ old('deskripsi', $item?->deskripsi) }}</textarea>
    <label for="foto">Foto (JPG, PNG, atau WebP, maks 2 MB)</label>
    <input type="file" id="foto" name="foto" class="input" accept="image/jpeg,image/png,image/webp" data-preview="#pratinjau">
    @if ($item) <small class="teks-lembut">Kosongkan untuk mempertahankan foto saat ini.</small> @endif
    <img id="pratinjau" class="preview" src="{{ $fotoLama }}" alt="Pratinjau foto" @if(!$fotoLama) hidden @endif>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
        <a href="{{ route('produk.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
