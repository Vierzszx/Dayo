@extends('layouts.app')

@section('content')
    <h1>Daftar akun</h1>

    <form class="form" method="POST" action="{{ route('register') }}">
        @csrf

        <label>
            Nama
            <input
                type="text"
                name="nama"
                value="{{ old('nama') }}"
                required
                maxlength="100"
                autocomplete="name"
            >
        </label>

        <label>
            Peran
            <select name="peran" required>
                <option
                    value="buyer"
                    @selected(old('peran', 'buyer') === 'buyer')
                >
                    Pembeli
                </option>

                <option
                    value="seller"
                    @selected(old('peran') === 'seller')
                >
                    Penjual
                </option>
            </select>
        </label>

        <label>
            Email
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
            >
        </label>

        <label>
            Password
            <input
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
            >
        </label>

        <label>
            Konfirmasi password
            <input
                type="password"
                name="password_confirmation"
                required
                minlength="8"
                autocomplete="new-password"
            >
        </label>

        <button type="submit">Daftar</button>
    </form>

    <p>
        Sudah punya akun?
        <a href="{{ route('login') }}">Login</a>
    </p>
@endsection