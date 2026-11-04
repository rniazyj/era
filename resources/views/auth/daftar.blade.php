@extends('layouts.auth')

@section('title', 'Daftar - ERA')
@section('palette', 'register')

@section('cta')
  <a href="{{ route('login') }}" class="toplink" data-wipe="Masuk">Masuk</a>
@endsection

@section('eyebrow')
  BUAT <span class="o">AKUN BARU</span>
@endsection

@section('heading')
  Gabung bareng ERA,<br><span class="dim">mulai dari sini.</span>
@endsection

@section('lead', 'Cuma semenit. Habis itu kamu bisa langsung lamar lowongan dan program magang yang cocok.')

@section('content')
  @error('error')
    <div class="note bad rise" style="--d:1">{{ $message }}</div>
  @enderror

  <form action="{{ route('register') }}" method="POST" data-loading>
    @csrf
    <div class="fields">
      <div class="row rise" style="--d:2">
        <div class="cell @error('name') bad @enderror">
          <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder=" " required autofocus autocomplete="name">
          <label for="name">Nama lengkap</label>
        </div>
      </div>
      @error('name')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:3">
        <div class="cell @error('email') bad @enderror">
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="email">
          <label for="email">E-mail</label>
        </div>
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:4">
        <div class="cell @error('password') bad @enderror">
          <input type="password" id="password" name="password" placeholder=" " required autocomplete="new-password">
          <label for="password">Password</label>
          <button type="button" class="eye" data-toggle-pw aria-label="Tampilkan password"></button>
        </div>
      </div>
      @error('password')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:5">
        <div class="cell">
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " required autocomplete="new-password">
          <label for="password_confirmation">Ulangi password</label>
          <button type="button" class="eye" data-toggle-pw aria-label="Tampilkan password"></button>
        </div>
      </div>

      <div class="pwmeter rise" style="--d:6" data-s="0">
        <div class="bars"><i></i><i></i><i></i><i></i></div>
        <span data-hint>Minimal 8 karakter</span>
        <em class="match" data-match></em>
      </div>

      <div class="row rise" style="--d:7">
        <button type="submit" class="go" data-busy="Bikin akunmu…"><span class="t">Buat akun</span><span class="a"><span>→</span></span></button>
      </div>
    </div>
  </form>
@endsection

@section('footnote')
  Sudah punya akun? <a href="{{ route('login') }}" class="u" data-wipe="Masuk">Masuk di sini</a>. Dengan mendaftar, kamu setuju dengan Kebijakan Privasi ERA.
@endsection