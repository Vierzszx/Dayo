<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RescueBite</title>

    <style>
        body {
            font: 16px system-ui;
            background: #f5f7f5;
            color: #18352a;
            margin: 0;
        }

        main,
        nav {
            max-width: 1100px;
            margin: auto;
            padding: 24px;
        }

        nav {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        a {
            color: #13734c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        td,
        th {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        input,
        textarea,
        select {
            padding: 10px;
            max-width: 100%;
            box-sizing: border-box;
        }

        label {
            display: block;
            margin: 16px 0;
        }

        label input,
        label textarea,
        label select {
            display: block;
            width: 100%;
        }

        button {
            padding: 9px 16px;
            background: #16764e;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .form {
            max-width: 600px;
        }

        .errors {
            color: #a22121;
        }

        .success {
            color: #116c3f;
        }

        .inline {
            display: inline;
        }

        small {
            display: block;
            color: #52655b;
        }

        .table-wrap {
            overflow: auto;
        }
    </style>
</head>

<body>
    <nav>
        <strong>RescueBite</strong>

        @auth
            <a href="{{ route('penawaran.index') }}">Penawaran</a>
            <a href="{{ route('pesanan.index') }}">Pesanan</a>

            @if(auth()->user()->peran === 'seller')
                <a href="{{ route('toko.index') }}">Toko</a>
                <a href="{{ route('produk.index') }}">Produk</a>
            @endif

            <span>{{ auth()->user()->nama }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Daftar</a>
        @endauth
    </nav>

    <main>
        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <ul class="errors">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @yield('content')
    </main>
</body>
</html>