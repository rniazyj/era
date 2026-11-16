{{-- resources/views/admin/konten/_pesan.blade.php : pesan sukses / gagal --}}
@if(session('success'))
  <div class="kw-flash ok" role="status">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="kw-flash err" role="alert">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="kw-flash err" role="alert">
    <div>
      <strong>Data belum tersimpan, periksa isian berikut:</strong>
      <ul>@foreach($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach</ul>
    </div>
  </div>
@endif