{{-- Edit toko. Variabel: $toko --}}
@extends('layouts.dayo')
@section('judul', 'Edit Toko')
@section('konten')
<div class="sempit"><h1 class="judul-halaman">Edit Toko</h1>
@include('toko._form', ['item' => $toko])</div>
@endsection
