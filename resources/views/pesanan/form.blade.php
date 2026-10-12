@extends('layouts.app')

@section('content')
    <h1>{{ $item ? 'Kelola pesanan' : 'Buat pesanan' }}</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if(!$item && $offers->isEmpty())
        <p>Belum ada penawaran yang tersedia untuk dipesan.</p>
    @else
        <form
            class="form"
            method="POST"
            action="{{ $item
                ? route('pesanan.update', $item->pesanan_id)
                : route('pesanan.store') }}"
        >
            @csrf

            @if($item)
                @method('PUT')
            @endif

            @if(!$item)
                <label>
                    Penawaran

                    <select name="penawaran_id" required>
                        @foreach($offers as $offer)
                            <option
                                value="{{ $offer->penawaran_id }}"
                                @selected(
                                    (string) old('penawaran_id', $selected)
                                    === (string) $offer->penawaran_id
                                )
                            >
                                {{ $offer->produk->nama_produk }}
                                — {{ $offer->produk->toko->nama_toko }}
                                — Rp{{ number_format($offer->harga_diskon, 2, ',', '.') }}
                                — stok {{ $offer->stok }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @else
                <p>{{ $item->penawaran->produk->nama_produk }}</p>
                <p>Kode pengambilan: {{ $item->kode_pengambilan }}</p>
                <p>Status saat ini: {{ $item->status }}</p>

                <label>
                    Tindakan

                    <select name="status" required>
                        @if(auth()->user()->peran === 'buyer')
                            @if($item->status === 'pending')
                                <option
                                    value="pending"
                                    @selected(old('status') === 'pending')
                                >
                                    Ubah jumlah
                                </option>
                            @endif
                        @elseif(auth()->user()->peran === 'seller')
                            @if($item->status === 'pending')
                                <option
                                    value="siap_diambil"
                                    @selected(old('status') === 'siap_diambil')
                                >
                                    Siap diambil
                                </option>
                            @endif

                            <option
                                value="selesai"
                                @selected(old('status') === 'selesai')
                            >
                                Selesai / sudah dibayar dan diambil
                            </option>
                        @endif

                        <option
                            value="dibatalkan"
                            @selected(old('status') === 'dibatalkan')
                        >
                            Batalkan
                        </option>
                    </select>
                </label>
            @endif

            @if(
                !$item
                || (
                    auth()->user()->peran === 'buyer'
                    && $item->status === 'pending'
                )
            )
                <label>
                    Jumlah

                    <input
                        type="number"
                        name="jumlah"
                        min="1"
                        max="100000"
                        value="{{ old('jumlah', $item?->jumlah ?? 1) }}"
                        required
                    >
                </label>
            @endif

            <p>
                Pembayaran dilakukan saat pengambilan.
                Total dihitung oleh server.
            </p>

            <button type="submit">Simpan</button>
        </form>
    @endif
@endsection