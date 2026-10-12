{{-- Form tambah/edit produk. $item = model produk atau null; $daftarToko = toko milik penjual. --}}
@php
    $m = $item ?? null;
    $fotoLama = $m && $m->foto ? (\Illuminate\Support\Str::startsWith($m->foto, ['http','/']) ? $m->foto : asset('storage/'.$m->foto)) : '';
@endphp
{{-- enctype multipart wajib agar foto bisa diunggah --}}
<form method="POST" action="{{ $m ? route('produk.update', $m->id) : route('produk.store') }}" class="panel" enctype="multipart/form-data">
    @csrf @if ($m) @method('PUT') @endif
    <label for="toko_id">Toko</label>
    <select id="toko_id" name="toko_id" class="input" required>
        <option value="">Pilih toko</option>
        @foreach ($daftarToko as $t)
            <option value="{{ $t->id }}" @selected((int) old('toko_id', optional($m)->toko_id) === (int) $t->id)>{{ $t->nama }}</option>
        @endforeach
    </select>
    <label for="nama">Nama produk</label>
    <input type="text" id="nama" name="nama" class="input" value="{{ old('nama', optional($m)->nama) }}" required>
    <label for="harga">Harga normal (Rp)</label>
    <input type="number" id="harga" name="harga" class="input" min="0" value="{{ old('harga', optional($m)->harga) }}" required>
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" class="input">{{ old('deskripsi', optional($m)->deskripsi) }}</textarea>
    <label for="foto">Foto</label>
    <input type="file" id="foto" name="foto" class="input" accept="image/*" data-preview="#pratinjau">
    <img id="pratinjau" class="preview" src="{{ $fotoLama }}" alt="Pratinjau foto" @if(!$fotoLama) hidden @endif>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
        <a href="{{ route('produk.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
