{{-- Form tambah/edit toko (dipakai create & edit). $item = model toko atau null. --}}
@php $m = $item ?? null; @endphp
<form method="POST" action="{{ $m ? route('toko.update', $m->id) : route('toko.store') }}" class="panel">
    @csrf @if ($m) @method('PUT') @endif
    <label for="nama">Nama toko</label>
    <input type="text" id="nama" name="nama" class="input" value="{{ old('nama', optional($m)->nama) }}" required>
    <label for="alamat">Alamat / lokasi</label>
    <input type="text" id="alamat" name="alamat" class="input" value="{{ old('alamat', optional($m)->alamat) }}">
    <label for="no_wa">No. WhatsApp</label>
    <input type="text" id="no_wa" name="no_wa" class="input" placeholder="628123456789" value="{{ old('no_wa', optional($m)->no_wa) }}">
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" class="input">{{ old('deskripsi', optional($m)->deskripsi) }}</textarea>
    <div class="baris-aksi">
        <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
        <a href="{{ route('toko.index') }}" class="btn btn--garis">Batal</a>
    </div>
</form>
