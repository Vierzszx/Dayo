{{--
    Backend (CrudController@index) selalu merender view ini dengan: $items (paginator), $resource, $fields.
    Tampilan per resource ada di toko/_index, produk/_index, penawaran/_index.
--}}
@extends('layouts.dayo')
@section('judul', ucfirst($resource))
@section('konten')
    @include($resource.'._index')
@endsection
