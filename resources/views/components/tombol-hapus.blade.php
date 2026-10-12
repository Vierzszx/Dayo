{{-- Tombol Hapus dengan konfirmasi (ditangani dayo.js lewat atribut data-konfirmasi). --}}
@props(['action', 'pesan' => 'Yakin ingin menghapus data ini?'])
<form method="POST" action="{{ $action }}" data-konfirmasi="{{ $pesan }}" class="form-inline">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn--bahaya btn--kecil">Hapus</button>
</form>
