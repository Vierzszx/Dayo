@extends('layouts.app')

@section('content')
    <h1>{{ ucfirst($resource) }}</h1>

    @if(auth()->user()->peran === 'seller')
        <p>
            <a href="{{ route($resource.'.create') }}">
                Tambah {{ $resource }}
            </a>
        </p>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>

                    @foreach($fields as $field => $type)
                        <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                    @endforeach

                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->getKey() }}</td>

                        @foreach($fields as $field => $type)
                            <td>
                                @if($type === 'file')
                                    @if($item->$field)
                                        <img
                                            src="{{ asset('storage/'.$item->$field) }}"
                                            alt="Foto produk"
                                            width="80"
                                        >
                                    @else
                                        -
                                    @endif
                                @elseif($type === 'datetime-local')
                                    {{ $item->$field?->format('d/m/Y H:i') }}
                                @else
                                    {{ $item->$field }}
                                @endif
                            </td>
                        @endforeach

                        <td>
                            @if($resource === 'penawaran')
                                <small>
                                    {{ $item->produk->nama_produk }}
                                    — {{ $item->produk->toko->nama_toko }}
                                </small>
                            @endif

                            <a href="{{ route($resource.'.show', $item->getKey()) }}">
                                Detail
                            </a>

                            @if(auth()->user()->peran === 'seller')
                                <a href="{{ route($resource.'.edit', $item->getKey()) }}">
                                    Edit
                                </a>

                                <form
                                    class="inline"
                                    method="POST"
                                    action="{{ route($resource.'.destroy', $item->getKey()) }}"
                                    onsubmit="return confirm('Hapus data ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit">Hapus</button>
                                </form>
                            @elseif(
                                auth()->user()->peran === 'buyer'
                                && $resource === 'penawaran'
                            )
                                @if(
                                    $item->status === 'aktif'
                                    && $item->stok > 0
                                    && $item->waktu_mulai->lte(now())
                                    && $item->batas_pengambilan->gt(now())
                                )
                                    <a href="{{ route('pesanan.create', [
                                        'penawaran_id' => $item->getKey()
                                    ]) }}">
                                        Pesan
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($fields) + 2 }}">
                            Belum ada data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
@endsection