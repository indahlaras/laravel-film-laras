<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin | OneMovies</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #172033;
            background: #f4f6fb;
            font-synthesis: none;
        }

        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }
        .layout { min-height: 100vh; }
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 2;
            display: flex;
            width: 248px;
            flex-direction: column;
            padding: 28px 18px;
            color: #d7deee;
            background: #151d32;
        }
        .brand { display: flex; align-items: center; gap: 11px; padding: 0 10px 38px; color: #fff; font-size: 19px; font-weight: 750; }
        .brand-mark { display: grid; width: 36px; height: 36px; place-items: center; border-radius: 12px; color: #172033; background: #79e0c1; font-size: 17px; }
        .nav-label { padding: 0 11px 11px; color: #8490a8; font-size: 10px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .nav-link { display: flex; align-items: center; gap: 12px; margin: 3px 0; padding: 12px 11px; border-radius: 10px; color: #b7c1d5; font-size: 13px; }
        .nav-link.active, .nav-link:hover { color: #fff; background: #27324a; }
        .nav-icon { display: inline-grid; width: 22px; place-items: center; color: #79e0c1; font-size: 16px; }
        .sidebar-bottom { margin-top: auto; border-top: 1px solid #2a344a; padding-top: 17px; }
        .sidebar-bottom .nav-link { width: 100%; border: 0; background: transparent; text-align: left; cursor: pointer; }
        .main { min-height: 100vh; margin-left: 248px; }
        .topbar { display: flex; min-height: 78px; align-items: center; justify-content: space-between; gap: 20px; border-bottom: 1px solid #e8ebf2; padding: 0 38px; background: #fff; }
        .breadcrumb { color: #7d8799; font-size: 12px; }
        .breadcrumb strong { color: #26334a; font-weight: 650; }
        .admin-chip { display: flex; align-items: center; gap: 10px; color: #303b50; font-size: 12px; font-weight: 650; }
        .avatar { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 50%; color: #174e43; background: #d7f4eb; font-weight: 750; }
        .content { width: min(1180px, 100%); margin: 0 auto; padding: 35px 38px 48px; }
        .welcome { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 27px; }
        .eyebrow { margin: 0 0 8px; color: #568b7d; font-size: 11px; font-weight: 750; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0; color: #172033; font-size: clamp(24px, 3vw, 31px); letter-spacing: -.04em; }
        .welcome-copy { margin: 8px 0 0; color: #7d8799; font-size: 13px; }
        .primary-link { display: inline-flex; align-items: center; gap: 8px; border-radius: 9px; padding: 11px 15px; color: #12392f; background: #79e0c1; font-size: 12px; font-weight: 750; }
        .primary-link:hover { background: #63d2b1; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 17px; margin-bottom: 25px; }
        .stat-card, .panel { border: 1px solid #e9edf4; border-radius: 14px; background: #fff; box-shadow: 0 6px 22px rgb(22 34 58 / 3%); }
        .stat-card { display: flex; align-items: center; justify-content: space-between; min-height: 120px; padding: 20px 21px; }
        .stat-label { margin: 0 0 11px; color: #788398; font-size: 12px; font-weight: 600; }
        .stat-value { margin: 0; color: #172033; font-size: 30px; font-weight: 750; letter-spacing: -.04em; }
        .stat-icon { display: grid; width: 43px; height: 43px; place-items: center; border-radius: 13px; color: #247761; background: #e2f7f0; font-size: 18px; }
        .stat-card:nth-child(2) .stat-icon { color: #6654a1; background: #eee9fb; }
        .stat-card:nth-child(3) .stat-icon { color: #a15f2d; background: #fff0e2; }
        .panel { overflow: hidden; margin-bottom: 22px; }
        .panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; border-bottom: 1px solid #edf0f5; padding: 19px 22px; }
        .panel-heading h2 { margin: 0; color: #202b40; font-size: 14px; font-weight: 750; }
        .panel-heading p { margin: 5px 0 0; color: #8a93a3; font-size: 11px; }
        .text-link { color: #398b75; font-size: 11px; font-weight: 700; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 20px; color: #8791a3; background: #fafbfc; font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        td { border-top: 1px solid #f0f2f6; padding: 13px 20px; color: #576176; font-size: 12px; }
        .film-cell { display: flex; min-width: 220px; align-items: center; gap: 12px; }
        .poster { width: 39px; height: 52px; flex: 0 0 auto; border-radius: 6px; background: #eef1f6; object-fit: cover; }
        .film-title { display: block; max-width: 310px; overflow: hidden; color: #26334a; font-size: 12px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .film-date { display: block; margin-top: 5px; color: #929bad; font-size: 10px; }
        .badge { display: inline-block; border-radius: 20px; padding: 5px 9px; color: #27725f; background: #e5f7f1; font-size: 10px; font-weight: 700; }
        .user-name { color: #26334a; font-weight: 700; }
        .empty { padding: 25px 20px; color: #8490a3; font-size: 12px; text-align: center; }
        @media (max-width: 900px) {
            .sidebar { width: 205px; }
            .main { margin-left: 205px; }
            .topbar { padding: 0 24px; }
            .content { padding: 28px 24px 40px; }
        }
        @media (max-width: 650px) {
            .sidebar { position: static; width: auto; min-height: auto; padding: 14px 16px; }
            .brand { padding: 0 2px 13px; }
            .nav-label, .sidebar-bottom { display: none; }
            .sidebar nav { display: flex; gap: 5px; overflow-x: auto; }
            .nav-link { flex: 0 0 auto; margin: 0; padding: 9px 10px; font-size: 11px; }
            .main { margin-left: 0; }
            .topbar { min-height: 60px; padding: 0 17px; }
            .content { padding: 25px 16px 35px; }
            .welcome { align-items: flex-start; flex-direction: column; }
            .stats { grid-template-columns: 1fr; gap: 10px; }
            .stat-card { min-height: 94px; }
            .panel-heading { padding: 16px; }
            th, td { padding-right: 14px; padding-left: 14px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <span class="brand-mark" aria-hidden="true">▶</span>
            <span>OneMovies</span>
        </a>
        <span class="nav-label">Menu utama</span>
        <nav aria-label="Navigasi dashboard">
            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon" aria-hidden="true">⌂</span>Dashboard
            </a>
            <a class="nav-link" href="#film-terbaru">
                <span class="nav-icon" aria-hidden="true">▣</span>Film terbaru
            </a>
            <a class="nav-link" href="#pengguna">
                <span class="nav-icon" aria-hidden="true">♙</span>Pengguna
            </a>
            <a class="nav-link" href="{{ route('movies') }}">
                <span class="nav-icon" aria-hidden="true">↗</span>Halaman film
            </a>
        </nav>
        <div class="sidebar-bottom">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button class="nav-link" type="submit">
                    <span class="nav-icon" aria-hidden="true">⇥</span>Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="breadcrumb">OneMovies <span aria-hidden="true">/</span> <strong>Dashboard</strong></div>
            <div class="admin-chip">
                <span class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span>{{ auth()->user()->name }} · Admin</span>
            </div>
        </header>

        <div class="content">
            <section class="welcome" aria-labelledby="welcome-title">
                <div>
                    <p class="eyebrow">Ringkasan aplikasi</p>
                    <h1 id="welcome-title">Selamat datang, {{ auth()->user()->name }}!</h1>
                    <p class="welcome-copy">Pantau koleksi film dan aktivitas pengguna dari satu tempat.</p>
                </div>
                <a class="primary-link" href="{{ route('movies') }}">Lihat katalog film <span aria-hidden="true">→</span></a>
            </section>

            <section class="stats" aria-label="Statistik aplikasi">
                <article class="stat-card">
                    <div>
                        <p class="stat-label">Total film</p>
                        <p class="stat-value">{{ number_format($filmCount) }}</p>
                    </div>
                    <span class="stat-icon" aria-hidden="true">▣</span>
                </article>
                <article class="stat-card">
                    <div>
                        <p class="stat-label">Genre tersedia</p>
                        <p class="stat-value">{{ number_format($genreCount) }}</p>
                    </div>
                    <span class="stat-icon" aria-hidden="true">◈</span>
                </article>
                <article class="stat-card">
                    <div>
                        <p class="stat-label">Pengguna terdaftar</p>
                        <p class="stat-value">{{ number_format($userCount) }}</p>
                    </div>
                    <span class="stat-icon" aria-hidden="true">♙</span>
                </article>
            </section>

            <section class="panel" id="film-terbaru" aria-labelledby="film-title">
                <div class="panel-heading">
                    <div>
                        <h2 id="film-title">Film terbaru</h2>
                        <p>Film yang terakhir ditambahkan ke katalog.</p>
                    </div>
                    <a class="text-link" href="{{ route('movies') }}">Lihat katalog →</a>
                </div>
                @if ($recentFilms->isEmpty())
                    <p class="empty">Belum ada film di katalog.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Film</th>
                                    <th>Genre</th>
                                    <th>Tahun</th>
                                    <th>Ditambahkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentFilms as $film)
                                    <tr>
                                        <td>
                                            <a class="film-cell" href="{{ route('movies.show', $film) }}">
                                                <img class="poster" src="{{ asset($film->poster) }}" alt="" loading="lazy">
                                                <span>
                                                    <span class="film-title">{{ $film->title }}</span>
                                                    <span class="film-date">ID film #{{ $film->id }}</span>
                                                </span>
                                            </a>
                                        </td>
                                        <td><span class="badge">{{ $film->genre?->name ?? 'Tanpa genre' }}</span></td>
                                        <td>{{ $film->year }}</td>
                                        <td>{{ $film->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="panel" id="pengguna" aria-labelledby="user-title">
                <div class="panel-heading">
                    <div>
                        <h2 id="user-title">Pengguna terbaru</h2>
                        <p>Akun yang baru terdaftar di OneMovies.</p>
                    </div>
                </div>
                @if ($recentUsers->isEmpty())
                    <p class="empty">Belum ada pengguna terdaftar.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama pengguna</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentUsers as $user)
                                    <tr>
                                        <td class="user-name">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge">{{ ucfirst($user->role?->name ?? 'user') }}</span></td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
</body>
</html>
