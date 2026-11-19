@extends('pelamar.profil.layout')

@section('konten')

{{-- Notifikasi --}}
@if (session('success') || session('success_password'))
    <div class="account-alert success">
        ✓ {{ session('success') ?? session('success_password') }}
    </div>
@endif

@if (session('info') || session('status'))
    <div class="account-alert info">
        {{ session('info') ?? session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="account-alert error">
        Periksa kembali data yang dimasukkan:
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="account-grid">

    {{-- INFORMASI EMAIL --}}
    <section class="account-card">

        <div class="account-card-head">
            <div class="account-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="m22 7-10 6L2 7"/>
                </svg>
            </div>

            <div>
                <h3>Informasi Email</h3>
                <p>Ubah alamat email akun Anda</p>
            </div>
        </div>

        <div class="account-card-body">

            <div class="account-current">
                <span class="account-current-label">Email saat ini</span>

                <div class="account-current-value">
                    {{ $user->email }}
                </div>

                @if ($user->hasVerifiedEmail())
                    <span class="account-status verified">Terverifikasi</span>
                @else
                    <span class="account-status unverified">Belum diverifikasi</span>
                @endif
            </div>

            <form action="{{ route('pelamar.profil.pengaturan-akun.email') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="account-field">
                    <label for="email">Email Baru</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email baru"
                        required
                        autocomplete="email">
                </div>

                <div class="account-field">
                    <label for="password_lama_email">Password Saat Ini</label>
                    <input
                        type="password"
                        id="password_lama_email"
                        name="password_lama"
                        placeholder="Masukkan password saat ini"
                        required
                        autocomplete="current-password">
                </div>

                <div class="account-note">
                    <strong>Perhatian:</strong> Setelah email diubah, Anda harus
                    melakukan verifikasi melalui tautan yang dikirim ke email baru.
                    Email baru belum dapat digunakan sebelum diverifikasi.
                </div>

                <button type="submit" class="account-submit">
                    Perbarui Email
                </button>
            </form>

        </div>
    </section>

    {{-- KEAMANAN PASSWORD --}}
    <section class="account-card">

        <div class="account-card-head">
            <div class="account-icon">
                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l.06.06a1.65 1.65 0 0 0-.33 1.82v0a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
                </svg>
            </div>

            <div>
                <h3>Keamanan Password</h3>
                <p>Perbarui password akun Anda</p>
            </div>
        </div>

        <div class="account-card-body">

            <form action="{{ route('pelamar.profil.pengaturan-akun.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="account-field">
                    <label for="password_lama">Password Saat Ini</label>
                    <input
                        type="password"
                        id="password_lama"
                        name="password_lama"
                        placeholder="Masukkan password saat ini"
                        required
                        autocomplete="current-password">
                </div>

                <div class="account-field">
                    <label for="password_baru">Password Baru</label>
                    <input
                        type="password"
                        id="password_baru"
                        name="password_baru"
                        placeholder="Minimal 8 karakter"
                        required
                        minlength="8"
                        autocomplete="new-password">
                </div>

                <div class="account-field">
                    <label for="password_baru_confirmation">Konfirmasi Password Baru</label>
                    <input
                        type="password"
                        id="password_baru_confirmation"
                        name="password_baru_confirmation"
                        placeholder="Ulangi password baru"
                        required
                        minlength="8"
                        autocomplete="new-password">
                </div>

                <div class="account-note">
                    Gunakan password minimal 8 karakter dan jangan membagikannya
                    kepada siapa pun.
                </div>

                <button type="submit" class="account-submit">
                    Perbarui Password
                </button>
            </form>

        </div>
    </section>

</div>

@endsection
