{{-- Tambah produk. Variabel: $daftarToko --}}
@extends('layouts.dayo')
@section('judul', 'Tambah Produk')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Tambah Produk</h1>
@include('produk._form', ['item' => null])</div>
@endsection
