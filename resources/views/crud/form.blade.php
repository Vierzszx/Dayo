@extends('layouts.app')

@section('content')
    <h1>{{ $item ? 'Edit' : 'Tambah' }} {{ $resource }}</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        class="form"
        method="POST"
        enctype="multipart/form-data"
        action="{{ $item
            ? route($resource.'.update', $item->getKey())
            : route($resource.'.store') }}"
    >
        @csrf

        @if($item)
            @method('PUT')
        @endif

        @foreach($fields as $field => $type)
            @php
                $value = $type === 'file'
                    ? null
                    : old($field, $item?->$field);

                if (
                    $type === 'datetime-local'
                    && $value instanceof \DateTimeInterface
                ) {
                    $value = $value->format('Y-m-d\TH:i');
                }

                $isPrice = in_array(
                    $field,
                    ['harga_normal', 'harga_diskon'],
                    true
                );
            @endphp

            <label>
                {{ ucwords(str_replace('_', ' ', $field)) }}

                @if($type === 'textarea')
                    <textarea name="{{ $field }}">{{ $value }}</textarea>

                @elseif($type === 'select')
                    <select name="{{ $field }}" required>
                        <option value="">Pilih</option>

                        @foreach(($options[$field] ?? []) as $id => $label)
                            <option
                                value="{{ $id }}"
                                @selected((string) $value === (string) $id)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                @elseif($type === 'file')
                    <input
                        type="file"
                        name="{{ $field }}"
                        accept="image/jpeg,image/png,image/webp"
                    >

                @else
                    <input
                        type="{{ $type }}"
                        name="{{ $field }}"
                        value="{{ $value }}"
                        @if($type === 'number')
                            min="{{ $isPrice ? '0.01' : '0' }}"
                            step="{{ $isPrice ? '0.01' : '1' }}"
                        @endif
                    >
                @endif

                @if($field === 'foto')
                    <small>
                        Foto JPG, PNG, atau WebP, maksimal 2 MB.
                        Kosongkan saat edit untuk mempertahankan foto.
                    </small>
                @endif
            </label>
        @endforeach

        <button type="submit">Simpan</button>

        <a href="{{ route($resource.'.index') }}">Kembali</a>
    </form>
@endsection