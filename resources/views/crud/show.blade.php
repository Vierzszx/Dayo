@extends('layouts.app')

@section('content')
    <h1>Detail {{ $resource }} #{{ $item->getKey() }}</h1>

    <dl>
        @foreach($fields as $field => $type)
            <dt>{{ ucwords(str_replace('_', ' ', $field)) }}</dt>

            <dd>
                @if($type === 'file')
                    @if($item->$field)
                        <img
                            src="{{ asset('storage/'.$item->$field) }}"
                            alt="Foto produk"
                            width="200"
                        >
                    @else
                        Tidak ada foto.
                    @endif
                @elseif($type === 'datetime-local')
                    {{ $item->$field?->format('d/m/Y H:i') }}
                @else
                    {{ $item->$field }}
                @endif
            </dd>
        @endforeach
    </dl>

    <a href="{{ route($resource.'.index') }}">Kembali</a>
@endsection