{{--
    Form pesanan. Backend (PesananController@create/edit):
      - Buat : $item = null, $offers (Penawaran aktif dengan produk.toko), $selected (dari ?penawaran_id=)
               POST pesanan.store -> penawaran_id, jumlah
      - Kelola: $item (Pesanan), PUT pesanan.update -> status (+ jumlah untuk pembeli saat pending)
    Total akhir selalu dihitung ulang server.
--}}
@extends('layouts.dayo')
@section('judul', $item ? 'Kelola pesanan' : 'Buat pesanan')
@section('konten')
@php $peran = auth()->user()->peran; @endphp
<div class="sempit">
    <h1 class="judul-halaman">{{ $item ? 'Kelola pesanan #'.$item->pesanan_id : 'Buat pesanan' }}</h1>
    <p class="teks-lembut">Pembayaran dilakukan langsung di toko saat mengambil makanan.</p>

    @if (! $item && $offers->isEmpty())
        <div class="panel"><p class="teks-lembut" style="margin:0">Belum ada penawaran yang tersedia untuk dipesan.</p></div>
        <div class="baris-aksi"><a href="{{ route('penawaran.index') }}" class="btn btn--garis">Kembali</a></div>
    @else
    <form method="POST" action="{{ $item ? route('pesanan.update', $item->pesanan_id) : route('pesanan.store') }}" class="panel">
        @csrf @if ($item) @method('PUT') @endif

        @if (! $item)
            <label for="penawaran_id">Penawaran</label>
            <select id="penawaran_id" name="penawaran_id" class="input" data-pilih-penawaran required>
                @foreach ($offers as $o)
                    <option value="{{ $o->penawaran_id }}" data-harga="{{ $o->harga_diskon }}" data-stok="{{ $o->stok }}"
                        @selected((string) old('penawaran_id', $selected) === (string) $o->penawaran_id)>
                        {{ $o->produk->nama_produk }} - {{ $o->produk->toko->nama_toko }} - Rp {{ number_format($o->harga_diskon, 0, ',', '.') }} (sisa {{ $o->stok }})
                    </option>
                @endforeach
            </select>

            <label for="jumlah">Jumlah</label>
            <input type="number" id="jumlah" name="jumlah" class="input" min="1" max="100000" value="{{ old('jumlah', request('jumlah', 1)) }}" data-input-jumlah required>
            <p class="total">Perkiraan total: <strong data-total>Rp 0</strong></p>
            <small class="teks-lembut">Total akhir dihitung ulang oleh server.</small>
        @else
            <table class="tabel-rincian" style="margin-top:0">
                <tr><th>Produk</th><td>{{ $item->penawaran->produk->nama_produk }}</td></tr>
                <tr><th>Kode pengambilan</th><td class="teks-kode">{{ $item->kode_pengambilan }}</td></tr>
                <tr><th>Status saat ini</th><td><span class="badge badge--{{ $item->status }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td></tr>
            </table>

            <label for="status">Tindakan</label>
            <select id="status" name="status" class="input" required>
                @if ($peran === 'buyer' && $item->status === 'pending')
                    <option value="pending" @selected(old('status') === 'pending')>Ubah jumlah</option>
                @elseif ($peran === 'seller')
                    @if ($item->status === 'pending')
                        <option value="siap_diambil" @selected(old('status') === 'siap_diambil')>Siap diambil</option>
                    @endif
                    <option value="selesai" @selected(old('status') === 'selesai')>Selesai (sudah dibayar dan diambil)</option>
                @endif
                <option value="dibatalkan" @selected(old('status') === 'dibatalkan')>Batalkan pesanan</option>
            </select>

            @if ($peran === 'buyer' && $item->status === 'pending')
                <label for="jumlah">Jumlah (dipakai jika memilih "Ubah jumlah")</label>
                <input type="number" id="jumlah" name="jumlah" class="input" min="1" max="100000" value="{{ old('jumlah', $item->jumlah) }}">
            @endif
        @endif

        <div class="baris-aksi">
            <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
            <a href="{{ route('pesanan.index') }}" class="btn btn--garis">Batal</a>
        </div>
    </form>
    @endif
</div>
@endsection
