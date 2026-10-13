{{--
    Form penawaran. Variabel: $item (Penawaran|null), $options['produk_id'] (id => nama_produk, produk milik penjual).
    Field mengikuti PenawaranController: produk_id, harga_diskon, stok, waktu_mulai, batas_pengambilan.
    Aturan backend: harga diskon <= harga normal produk; batas > waktu mulai dan > sekarang;
    penawaran yang sudah dipesan tidak bisa diedit.
--}}
@php $dt = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d\TH:i') : ''; @endphp
<form method="POST" action="{{ $item ? route('penawaran.update', $item->penawaran_id) : route('penawaran.store') }}" class="panel">
    @csrf @if ($item) @method('PUT') @endif
    <label for="produk_id">Produk</label>
    <select id="produk_id" name="produk_id" class="input" required>
        <option value="">Pilih produk</option>
        @foreach (($options['produk_id'] ?? []) as $id => $nama)
            <option value="{{ $id }}" @selected((string) old('produk_id', $item?->produk_id) === (string) $id)>{{ $nama }}</option>
        @endforeach
    </select>
    @if (count($options['produk_id'] ?? []) === 0)
        <small class="teks-lembut">Belum ada produk. <a href="{{ route('produk.create') }}" class="tautan-aksi">Buat produk dulu</a>.</small>
    @endif
    <label for="harga_diskon">Harga diskon (Rp)</label>
    <input type="number" id="harga_diskon" name="harga_diskon" class="input" min="0.01" step="0.01" value="{{ old('harga_diskon', $item?->harga_diskon) }}" required>
    <label for="stok">Stok</label>
    <input type="number" id="stok" name="stok" class="input" min="0" max="100000" value="{{ old('stok', $item?->stok) }}" required>
    <label for="waktu_mulai">Waktu mulai</label>
    <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="input" value="{{ old('waktu_mulai', $dt($item?->waktu_mulai)) }}" required>
    <label for="batas_pengambilan">Batas pengambilan</label>
    <input type="datetime-local" id="batas_pengambilan" name="batas_pengambilan" class="input" value="{{ old('batas_pengambilan', $dt($item?->batas_pengambilan)) }}" required>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">{{ $item ? 'Simpan perubahan' : 'Tambah' }}</button>
        <a href="{{ route('penawaran.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
