{{-- Form tambah/edit penawaran. $item = model atau null; $daftarProduk = produk milik penjual. --}}
@php
    $m = $item ?? null;
    $dt = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d\TH:i') : '';   // format untuk input datetime-local
@endphp
<form method="POST" action="{{ $m ? route('penawaran.update', $m->id) : route('penawaran.store') }}" class="panel">
    @csrf @if ($m) @method('PUT') @endif
    <label for="produk_id">Produk</label>
    <select id="produk_id" name="produk_id" class="input" required>
        <option value="">Pilih produk</option>
        @foreach ($daftarProduk as $pr)
            <option value="{{ $pr->id }}" @selected((int) old('produk_id', optional($m)->produk_id) === (int) $pr->id)>{{ $pr->nama }}</option>
        @endforeach
    </select>
    <label for="harga_diskon">Harga diskon (Rp)</label>
    <input type="number" id="harga_diskon" name="harga_diskon" class="input" min="0" value="{{ old('harga_diskon', optional($m)->harga_diskon) }}" required>
    <label for="stok">Stok</label>
    <input type="number" id="stok" name="stok" class="input" min="1" value="{{ old('stok', optional($m)->stok) }}" required>
    <label for="waktu_mulai">Waktu mulai</label>
    <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="input" value="{{ old('waktu_mulai', $dt(optional($m)->waktu_mulai)) }}" required>
    <label for="batas_pengambilan">Batas pengambilan</label>
    <input type="datetime-local" id="batas_pengambilan" name="batas_pengambilan" class="input" value="{{ old('batas_pengambilan', $dt(optional($m)->batas_pengambilan)) }}" required>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">{{ $m ? 'Simpan perubahan' : 'Tambah' }}</button>
        <a href="{{ route('penawaran.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
