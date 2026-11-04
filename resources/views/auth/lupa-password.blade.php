@extends('layouts.auth')

@section('title', 'Lupa Password - ERA')
@section('palette', 'forgot')

@section('cta')
  <a href="{{ route('login') }}" class="toplink" data-wipe="Masuk">Masuk</a>
@endsection

@section('eyebrow')
  PEMULIHAN <span class="o">AKUN</span>
@endsection

@section('heading')
  Lupa password?<br><span class="dim">Santai aja.</span>
@endsection

@section('lead', 'Tulis e-mail yang kamu pakai buat daftar. Nanti kami kirim tautan untuk bikin password baru.')

@section('content')
  <form action="{{ route('password.email') }}" method="POST" data-loading>
    @csrf
    <div class="fields">
      <div class="row rise" style="--d:2">
        <div class="cell @error('email') bad @enderror">
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " required autofocus autocomplete="email">
          <label for="email">E-mail terdaftar</label>
        </div>
      </div>
      @error('email')<p class="err">{{ $message }}</p>@enderror

      <div class="row rise" style="--d:3">
        <button type="submit" class="go" data-busy="Lagi dikirim…"><span class="t">Kirim tautan reset</span><span class="a"><span>→</span></span></button>
      </div>
    </div>
  </form>
@endsection

@section('footnote')
  Tautannya cuma berlaku sebentar, jadi cek juga folder spam ya. Ingat password-nya? <a href="{{ route('login') }}" class="u" data-wipe="Masuk">Balik ke halaman masuk</a>.
@endsection