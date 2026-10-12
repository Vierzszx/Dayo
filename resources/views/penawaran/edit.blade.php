{{-- Edit penawaran. Variabel: $penawaran, $daftarProduk --}}
@extends('layouts.dayo')
@section('judul', 'Edit Penawaran')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Edit Penawaran</h1>
@include('penawaran._form', ['item' => $penawaran])</div>
@endsection
