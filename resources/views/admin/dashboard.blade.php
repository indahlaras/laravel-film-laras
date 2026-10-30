<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
</head>
<body>
    <main>
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, {{ auth()->user()->name }}.</p>
        <a href="{{ route('home') }}">Kembali ke halaman utama</a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
