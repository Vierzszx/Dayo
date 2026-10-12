{{-- Edit produk. Variabel: $produk, $daftarToko --}}
@extends('layouts.dayo')
@section('judul', 'Edit Produk')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Edit Produk</h1>
@include('produk._form', ['item' => $produk])</div>
@endsection
