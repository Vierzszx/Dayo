{{--
    Backend (CrudController@create/edit) merender view ini dengan: $item (model|null), $resource, $fields, $options.
    Form per resource ada di toko/_form, produk/_form, penawaran/_form.
--}}
@extends('layouts.dayo')
@section('judul', ($item ? 'Edit ' : 'Tambah ').ucfirst($resource))
@section('konten')
<div class="sempit">
    <h1 class="judul-halaman">{{ $item ? 'Edit' : 'Tambah' }} {{ ucfirst($resource) }}</h1>
    @include($resource.'._form')
</div>
@endsection
