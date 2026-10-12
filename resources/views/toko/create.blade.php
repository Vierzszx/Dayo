{{-- Tambah toko --}}
@extends('layouts.dayo')
@section('judul', 'Tambah Toko')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Tambah Toko</h1>
@include('toko._form', ['item' => null])</div>
@endsection
