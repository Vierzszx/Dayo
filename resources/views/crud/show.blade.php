{{--
    Backend (CrudController@show) merender view ini dengan: $item, $resource, $fields.
    Detail per resource ada di toko/_show, produk/_show, penawaran/_show.
--}}
@extends('layouts.dayo')
@section('judul', 'Detail '.ucfirst($resource))
@section('konten')
    @include($resource.'._show')
@endsection
