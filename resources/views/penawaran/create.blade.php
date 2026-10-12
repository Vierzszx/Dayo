{{-- Tambah penawaran. Variabel: $daftarProduk --}}
@extends('layouts.dayo')
@section('judul', 'Tambah Penawaran')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Tambah Penawaran</h1>
@include('penawaran._form', ['item' => null])</div>
@endsection
