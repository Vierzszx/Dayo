@extends('layouts.app')

@section('content')
    <h1>Pesanan</h1>

    @if(auth()->user()->peran === 'buyer')
        <a href="{{ route('pesanan.create') }}">Buat pesanan</a>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produk</th>
                    <th>Pembeli</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->pesanan_id }}</td>
                        <td>{{ $item->penawaran->produk->nama_produk }}</td>
                        <td>{{ $item->pembeli->nama }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>
                            Rp{{ number_format($item->total_harga, 2, ',', '.') }}
                        </td>
                        <td>{{ $item->status }}</td>
                        <td>
                            <a href="{{ route('pesanan.show', $item->pesanan_id) }}">
                                Detail
                            </a>

                            @if(in_array($item->status, ['pending', 'siap_diambil'], true))
                                <a href="{{ route('pesanan.edit', $item->pesanan_id) }}">
                                    Kelola
                                </a>
                            @endif

                            @if($item->status === 'dibatalkan')
                                <form
                                    class="inline"
                                    method="POST"
                                    action="{{ route('pesanan.destroy', $item->pesanan_id) }}"
                                    onsubmit="return confirm('Hapus pesanan dibatalkan ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
@endsection