@extends('layouts.auth')

@section('title', 'Masuk - ERA')
@section('palette', 'login')

@section('cta')
  <a href="{{ route('register') }}" class="toplink" data-wipe="Daftar">Daftar</a>
@endsection

@section('eyebrow')
  SELAMAT DATANG <span class="o">KEMBALI</span>
@endsection

@section('heading')
  Masuk dulu,<br><span class="dim">lanjutkan kariermu.</span>
@endsection

@section('lead', 'Lamaran dan progres seleksimu masih tersimpan rapi di sini. Tinggal masuk, langsung lanjut.')

@section('content')
  <form action="{{ route('login') }}" method="POST" data-loading>
    @csrf
    <div class="fields">
      <div class="row rise" style="--d:2">
        <div class="cell @error('email') bad @enderror">
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " required autofocus autocomplete="email">
          <label for="email">E-mail</label>
        </div>
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:3">
        <div class="cell @error('password') bad @enderror">
          <input type="password" id="password" name="password" placeholder=" " required autocomplete="current-password">
          <label for="password">Password</label>
          <button type="button" class="eye" data-toggle-pw aria-label="Tampilkan password"></button>
        </div>
      </div>
      @error('password')<p class="err">{{ $message }}</p>@enderror

      <div class="row opts rise" style="--d:4">
        <label class="chk"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Ingat saya</label>
        <a href="{{ route('password.request') }}" class="u" data-wipe="Lupa password">Lupa password?</a>
      </div>

      <div class="row rise" style="--d:5">
        <button type="submit" class="go" data-busy="Lagi dicek…"><span class="t">Masuk</span><span class="a"><span>→</span></span></button>
      </div>
    </div>
  </form>
@endsection

@section('footnote')
  Belum punya akun? <a href="{{ route('register') }}" class="u" data-wipe="Daftar">Daftar gratis</a>. Dengan masuk, kamu setuju dengan Kebijakan Privasi ERA.
@endsection