{{--
    Buat Pesanan. Variabel dari controller: $daftar (koleksi Penawaran yang masih tersedia).
    Query string opsional: ?penawaran={id}&jumlah={n}. Form dikirim POST ke pesanan.store
    dengan field: penawaran_id, jumlah.
--}}
@extends('layouts.dayo')
@section('judul', 'Buat pesanan')

@section('konten')
@php $terpilih = (int) request('penawaran', optional(collect($daftar)->first())->id ?? 0); @endphp
<div class="sempit">
    <h1 class="judul-halaman">Buat pesanan</h1>
    <p class="teks-lembut">Pembayaran dilakukan langsung di toko saat mengambil makanan.</p>

    <form method="POST" action="{{ route('pesanan.store') }}" class="panel" data-form-pesanan>
        @csrf
        <label for="penawaran">Penawaran</label>
        <select id="penawaran" name="penawaran_id" class="input" data-pilih-penawaran>
            @foreach ($daftar as $p)
                @php
                    $p = is_array($p) ? (object) $p : $p;
                    $namaToko = $p->toko_nama ?? optional($p->toko ?? null)->nama ?? '';
                @endphp
                <option value="{{ $p->id }}" data-harga="{{ $p->harga_diskon }}" data-stok="{{ $p->stok }}"
                    @selected((int) $p->id === $terpilih)>
                    {{ $p->nama }} - {{ $namaToko }} - Rp {{ number_format($p->harga_diskon, 0, ',', '.') }} (sisa {{ $p->stok }})
                </option>
            @endforeach
        </select>
        @error('penawaran_id') <small class="galat">{{ $message }}</small> @enderror

        <label for="jumlah">Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" class="input" min="1"
               value="{{ old('jumlah', request('jumlah', 1)) }}" data-input-jumlah>
        @error('jumlah') <small class="galat">{{ $message }}</small> @enderror

        <p class="total">Perkiraan total: <strong data-total>Rp 0</strong></p>
        <small class="teks-lembut">Total akhir dihitung ulang oleh server.</small>

        <div class="baris-aksi">
            <button type="submit" class="btn btn--utama btn--lebar">Simpan</button>
            <a href="{{ route('penawaran.index') }}" class="btn btn--garis">Batal</a>
        </div>
    </form>
</div>
@endsection
