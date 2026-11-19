
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verifikasi Email - ERA</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#faf9fc] flex items-center justify-center p-5">

    <div class="w-full max-w-md bg-white rounded-3xl border border-black/5 shadow-sm p-7 text-center">

        <div class="w-16 h-16 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-5">
            <span class="text-3xl">✉️</span>
        </div>

        <h1 class="text-xl font-bold text-zinc-800">
            Verifikasi Email Anda
        </h1>

        <p class="text-sm text-zinc-500 mt-3 leading-relaxed">
            Terima kasih telah mendaftar di ERA.
            Silakan periksa email Anda dan klik tautan verifikasi
            untuk mengaktifkan akses akun.
        </p>

        @if($email)
            <p class="text-sm font-semibold text-zinc-700 mt-4 break-all">
                {{ $email }}
            </p>
        @endif

        @if(session('status'))
            <div class="mt-5 rounded-xl bg-emerald-50 text-emerald-700 text-xs p-3">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-5 rounded-xl bg-rose-50 text-rose-600 text-xs p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('verification.send') }}"
              class="mt-6 space-y-3">

            @csrf

            <label class="block text-left text-xs font-semibold text-zinc-600">
                Email terdaftar
            </label>

            <input type="email"
                   name="email"
                   value="{{ old('email', $email) }}"
                   required
                   class="w-full rounded-xl border border-zinc-200 px-4 py-3 text-sm outline-none focus:border-orange-500">

            <button type="submit"
                    class="w-full rounded-full bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm py-3 transition">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <a href="{{ route('login') }}"
           class="inline-block mt-5 text-xs font-semibold text-zinc-400 hover:text-orange-500">
            Kembali ke Login
        </a>

    </div>

</body>
</html>
