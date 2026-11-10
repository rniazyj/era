@php
    $user = auth()->user();

    $nama = $user->name ?? 'Admin';
    $inisial = mb_strtoupper(mb_substr($nama, 0, 1));

    $role = optional($user->role)->nama_role ?? 'Admin HR';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Profil Admin - PT Equityworld Futures</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f5f5f7;
            --card: #ffffff;
            --ink: #171717;
            --ink-soft: #45454d;
            --muted: #858590;

            --orange: #f97316;
            --orange-dark: #ea580c;
            --orange-soft: #fff4ea;

            --line: rgba(15, 15, 17, .08);
            --radius: 22px;

            --shadow:
                0 1px 2px rgba(15,15,17,.04),
                0 8px 30px rgba(15,15,17,.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        button, input {
            font: inherit;
        }

        .wrap {
            max-width: 1320px;
            margin: auto;
            padding: 0 1.5rem;
        }

        /* HEADER */

        .head-wrap {
            margin-top: 1.5rem;
        }

        .profile-head {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .banner {
            height: 190px;
            background: linear-gradient(
                115deg,
                #ffb366,
                #f97316,
                #ea580c,
                #7c2d12,
                #171717,
                #ea580c,
                #ffb366
            );

            background-size: 400% 400%;
            animation: gradientMove 14s ease infinite;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 50%;
            }
        }

        .head-inner {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 0 1.75rem 1.75rem;
        }

        .avatar-wrap {
            width: 120px;
            height: 120px;
            flex: none;
            margin-top: -3.8rem;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 5px solid white;

            display: grid;
            place-items: center;

            background: var(--orange);
            color: white;

            font-size: 3rem;
            font-weight: 800;

            box-shadow: 0 10px 30px rgba(15,15,17,.18);
        }

        .hero-info {
            flex: 1;
            min-width: 0;
            padding-top: 1rem;
        }

        .hero-info h1 {
            font-size: clamp(1.5rem, 2.6vw, 2.1rem);
            font-weight: 800;
            letter-spacing: -.03em;
            line-height: 1.2;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem 1rem;
            margin-top: .65rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;

            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .badge svg {
            width: 15px;
            height: 15px;
        }

        .role-badge {
            background: var(--orange-soft);
            color: var(--orange-dark);
            border-radius: 99px;
            padding: .3rem .7rem;
        }

        .hero-description {
            margin-top: .8rem;
            color: var(--muted);
            font-size: .88rem;
        }

        /* MAIN LAYOUT */

        .layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 1.5rem;

            padding-top: 1.5rem;
            padding-bottom: 4rem;

            align-items: start;
        }

        /* SIDEBAR */

        .sidebar {
            position: sticky;
            top: 1.5rem;

            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .side-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.1rem;
        }

        .side-user {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .3rem;
        }

        .side-avatar {
            width: 52px;
            height: 52px;
            flex: none;

            border-radius: 50%;
            background: var(--orange);
            color: white;

            display: grid;
            place-items: center;

            font-size: 1.4rem;
            font-weight: 800;
        }

        .side-user h4 {
            font-size: .9rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .side-user p {
            font-size: .74rem;
            color: var(--muted);
        }

        .nav-label {
            font-size: .65rem;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;

            color: var(--muted);
            padding: .8rem .7rem .4rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;

            padding: .7rem .75rem;
            border-radius: 14px;

            color: var(--ink-soft);
            text-decoration: none;

            font-size: .85rem;
            font-weight: 600;

            transition: .2s;
        }

        .nav-item:hover {
            background: var(--orange-soft);
            color: var(--orange-dark);
        }

        .nav-item.active {
            background: var(--ink);
            color: white;
        }

        .nav-item.active .nav-icon {
            background: var(--orange);
            color: white;
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            flex: none;

            display: grid;
            place-items: center;

            background: #f4f4f6;
            color: var(--ink-soft);

            border-radius: 11px;
        }

        .nav-icon svg {
            width: 18px;
            height: 18px;
        }

        .logout-item {
            width: 100%;
            border: none;
            background: transparent;
            cursor: pointer;
            text-align: left;
            color: #dc2626;
        }

        .logout-item:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* CONTENT */

        .main {
            min-width: 0;

            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);

            padding: 1.7rem;
        }

        .card-head {
            display: flex;
            align-items: center;
            gap: .8rem;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 42px;
            height: 42px;
            flex: none;

            display: grid;
            place-items: center;

            border-radius: 13px;
            background: var(--orange-soft);
            color: var(--orange);
        }

        .card-icon svg {
            width: 20px;
            height: 20px;
        }

        .card-head h3 {
            font-size: 1rem;
            font-weight: 800;
        }

        .card-head p {
            font-size: .76rem;
            color: var(--muted);
        }

        .fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field label {
            font-size: .75rem;
            font-weight: 700;
            color: var(--ink-soft);
        }

        .input-wrap {
            position: relative;
        }

        .input {
            width: 100%;

            padding: .75rem .9rem;

            background: #fafafb;
            border: 1.5px solid #ececf0;
            border-radius: 13px;

            color: var(--ink);
            font-size: .88rem;
            font-weight: 500;

            transition: .2s;
        }

        .input:focus {
            outline: none;
            background: white;
            border-color: var(--orange);
            box-shadow: 0 0 0 4px rgba(249,115,22,.14);
        }

        .input:disabled {
            background: #f4f4f6;
            color: var(--muted);
            cursor: not-allowed;
        }

        .input::placeholder {
            color: #a9a9b4;
            font-weight: 400;
        }

        .password-input {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: .8rem;
            transform: translateY(-50%);

            border: none;
            background: transparent;

            color: var(--muted);
            cursor: pointer;

            font-size: .75rem;
            font-weight: 700;
        }

        .hint {
            font-size: .7rem;
            color: var(--muted);
        }

        .error {
            font-size: .72rem;
            color: #dc2626;
            font-weight: 600;
        }

        .alert {
            border-radius: 15px;
            padding: .9rem 1.1rem;

            font-size: .84rem;
            font-weight: 600;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
            gap: .7rem;

            margin-top: 1.5rem;
            padding-top: 1.2rem;

            border-top: 1px solid var(--line);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;

            padding: .7rem 1.3rem;

            border: none;
            border-radius: 99px;

            font-size: .82rem;
            font-weight: 700;

            cursor: pointer;
            text-decoration: none;

            transition: .2s;
        }

        .btn-primary {
            background: var(--orange);
            color: white;
        }

        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f4f4f6;
            color: var(--ink-soft);
        }

        .btn-secondary:hover {
            background: #e9e9ed;
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        /* RESPONSIVE */

        @media (max-width: 1000px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .side-nav {
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
            }

            .nav-label {
                width: 100%;
            }

            .nav-item {
                flex: 1;
                min-width: 150px;
            }
        }

        @media (max-width: 650px) {
            .wrap {
                padding: 0 1rem;
            }

            .banner {
                height: 135px;
            }

            .head-inner {
                flex-direction: column;
                gap: .3rem;
                padding: 0 1.1rem 1.2rem;
            }

            .avatar-wrap {
                width: 90px;
                height: 90px;
                margin-top: -2.8rem;
            }

            .hero-info {
                padding-top: .5rem;
            }

            .fields {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 1.2rem;
            }

            .card-footer {
                flex-direction: column-reverse;
            }

            .card-footer .btn {
                width: 100%;
            }

            .nav-item {
                min-width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .banner {
                animation: none;
            }
        }
    </style>
</head>

<body>

{{-- HEADER PROFIL --}}
<div class="wrap head-wrap">

    <header class="profile-head">

        <div class="banner"></div>

        <div class="head-inner">

            <div class="avatar-wrap">
                <div class="avatar">
                    {{ $inisial }}
                </div>
            </div>

            <div class="hero-info">

                <h1>{{ $nama }}</h1>

                <div class="badges">

                    <span class="badge">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-10 6L2 7"/>
                        </svg>

                        {{ $user->email }}
                    </span>

                    <span class="badge role-badge">
                        {{ $role }}
                    </span>

                </div>

                <p class="hero-description">
                    Kelola informasi akun dan keamanan akun admin kamu.
                </p>

            </div>

        </div>

    </header>

</div>


{{-- SIDEBAR + CONTENT --}}
<div class="wrap layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="side-card">

            <div class="side-user">

                <div class="side-avatar">
                    {{ $inisial }}
                </div>

                <div>
                    <h4>{{ $nama }}</h4>
                    <p>{{ $role }}</p>
                </div>

            </div>

        </div>


        <div class="side-card side-nav">

            <div class="nav-label">
                Menu Utama
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item">

                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </span>

                Dashboard

            </a>


            <a href="{{ route('admin.profil') }}"
               class="nav-item active">

                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </span>

                Akun Saya

            </a>


            <a href="#password"
               class="nav-item"
               id="menuPassword">

                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>

                Ganti Password

            </a>


            <div class="nav-label">
                Sistem
            </div>


            {{-- Logout --}}
            <form method="POST"
                  action="{{ route('logout') }}"
                  onsubmit="return confirm('Apakah kamu yakin ingin logout?')">

                @csrf

                <button type="submit"
                        class="nav-item logout-item">

                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 17l5-5-5-5"/>
                            <path d="M15 12H3"/>
                            <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                        </svg>
                    </span>

                    Logout

                </button>

            </form>

        </div>

    </aside>


    {{-- KONTEN --}}
    <main class="main">

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('success_password'))
            <div class="alert alert-success">
                ✓ {{ session('success_password') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                Ada {{ $errors->count() }} kesalahan yang perlu diperbaiki.
            </div>
        @endif


        {{-- AKUN SAYA --}}
        <section class="card" id="akun">

            <div class="card-head">

                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                </div>

                <div>
                    <h3>Akun Saya</h3>
                    <p>Informasi akun admin yang terdaftar</p>
                </div>

            </div>


            <form method="POST"
                  action="{{ route('admin.profil.update') }}">

                @csrf
                @method('PUT')

                <div class="fields">

                    {{-- Nama --}}
                    <div class="field">

                        <label for="name">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="input"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Masukkan nama lengkap"
                            required
                        >

                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div class="field">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="input"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Masukkan email"
                            required
                        >

                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror

                    </div>


                    {{-- Nomor Telepon --}}
                    <div class="field">

                        <label for="no_telepon">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="no_telepon"
                            name="no_telepon"
                            class="input"
                            value="{{ old('no_telepon', $user->no_telepon) }}"
                            placeholder="Masukkan nomor telepon"
                        >

                        @error('no_telepon')
                            <span class="error">{{ $message }}</span>
                        @enderror

                    </div>


                    {{-- Role --}}
                    <div class="field">

                        <label for="role">
                            Role
                        </label>

                        <input
                            type="text"
                            id="role"
                            class="input"
                            value="{{ $role }}"
                            disabled
                        >

                        <span class="hint">
                            Role akun ditentukan oleh sistem.
                        </span>

                    </div>

                </div>


                <div class="card-footer">

                    <button type="reset"
                            class="btn btn-secondary">
                        Reset
                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.4"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </section>


        {{-- GANTI PASSWORD --}}
        <section class="card" id="password">

            <div class="card-head">

                <div class="card-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>

                <div>
                    <h3>Ganti Password</h3>
                    <p>Perbarui password untuk menjaga keamanan akun</p>
                </div>

            </div>


            <form method="POST"
                  action="{{ route('admin.profil.password') }}">

                @csrf
                @method('PUT')

                <div class="fields">

                    {{-- Password lama --}}
                    <div class="field full">

                        <label for="password_lama">
                            Password Saat Ini
                        </label>

                        <div class="input-wrap">

                            <input
                                type="password"
                                id="password_lama"
                                name="password_lama"
                                class="input password-input"
                                placeholder="Masukkan password saat ini"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_lama">
                                Lihat
                            </button>

                        </div>

                        @error('password_lama')
                            <span class="error">{{ $message }}</span>
                        @enderror

                    </div>


                    {{-- Password baru --}}
                    <div class="field">

                        <label for="password_baru">
                            Password Baru
                        </label>

                        <div class="input-wrap">

                            <input
                                type="password"
                                id="password_baru"
                                name="password_baru"
                                class="input password-input"
                                placeholder="Minimal 8 karakter"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_baru">
                                Lihat
                            </button>

                        </div>

                        @error('password_baru')
                            <span class="error">{{ $message }}</span>
                        @enderror

                    </div>


                    {{-- Konfirmasi --}}
                    <div class="field">

                        <label for="password_baru_confirmation">
                            Konfirmasi Password Baru
                        </label>

                        <div class="input-wrap">

                            <input
                                type="password"
                                id="password_baru_confirmation"
                                name="password_baru_confirmation"
                                class="input password-input"
                                placeholder="Ulangi password baru"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password_baru_confirmation">
                                Lihat
                            </button>

                        </div>

                    </div>

                </div>


                <div class="card-footer">

                    <button type="reset"
                            class="btn btn-secondary">
                        Reset
                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.4"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>

                        Perbarui Password

                    </button>

                </div>

            </form>

        </section>

    </main>

</div>


<script>
    // Toggle tampil/sembunyikan password
    document.querySelectorAll('.toggle-password').forEach(button => {

        button.addEventListener('click', function () {

            const target = document.getElementById(
                this.dataset.target
            );

            if (!target) return;

            if (target.type === 'password') {
                target.type = 'text';
                this.textContent = 'Sembunyikan';
            } else {
                target.type = 'password';
                this.textContent = 'Lihat';
            }

        });

    });


    // Sidebar ganti password mengarah ke card password
    document.getElementById('menuPassword')
        ?.addEventListener('click', function () {

            document.querySelectorAll('.nav-item')
                .forEach(item => item.classList.remove('active'));

            this.classList.add('active');

        });
</script>

</body>
</html>