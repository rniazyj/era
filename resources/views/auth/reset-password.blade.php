@extends('layouts.auth')

@section('title', 'Reset Password - ERA')
@section('palette', 'reset')

@section('cta')
  <a href="{{ route('login') }}" class="toplink" data-wipe="Masuk">Masuk</a>
@endsection

@section('eyebrow')
  LANGKAH <span class="o">TERAKHIR</span>
@endsection

@section('heading')
  Bikin password<br><span class="dim">yang baru.</span>
@endsection

@section('lead', 'Pilih yang gampang kamu ingat tapi susah ditebak orang. Campur huruf besar, kecil, angka, dan simbol biar makin aman.')

@section('content')
  @error('token')
    <div class="note bad rise" style="--d:1">{{ $message }}</div>
  @enderror

  <form action="{{ route('password.update') }}" method="POST" data-loading>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="fields">
      <div class="row rise" style="--d:2">
        <div class="cell @error('email') bad @enderror">
          <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" placeholder=" " required autocomplete="email">
          <label for="email">E-mail terdaftar</label>
        </div>
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:3">
        <div class="cell @error('password') bad @enderror">
          <input type="password" id="password" name="password" placeholder=" " required autofocus autocomplete="new-password">
          <label for="password">Password baru</label>
          <button type="button" class="eye" data-toggle-pw aria-label="Tampilkan password"></button>
        </div>
      </div>
      @error('password')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:4">
        <div class="cell">
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " required autocomplete="new-password">
          <label for="password_confirmation">Ulangi password</label>
          <button type="button" class="eye" data-toggle-pw aria-label="Tampilkan password"></button>
        </div>
      </div>

      <div class="pwmeter rise" style="--d:5" data-s="0">
        <div class="bars"><i></i><i></i><i></i><i></i></div>
        <span data-hint>Minimal 8 karakter</span>
        <em class="match" data-match></em>
      </div>

      <div class="row rise" style="--d:6">
        <button type="submit" class="go" data-busy="Menyimpan…"><span class="t">Simpan password baru</span><span class="a"><span>→</span></span></button>
      </div>
    </div>
  </form>
@endsection

@section('footnote')
  Setelah password diganti, kamu tinggal masuk lagi pakai password yang baru.
@endsection