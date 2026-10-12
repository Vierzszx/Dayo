@extends('layouts.app')
@section('content')
<h1>Login</h1><form class="form" method="POST" action="{{ route('login') }}">@csrf
<label>Email<input type="email" name="email" value="{{ old('email') }}" required></label><label>Password<input type="password" name="password" required ></label><button>Simpan</button></form>@endsection