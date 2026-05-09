<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border p-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600 text-2xl font-bold">
            !
        </div>

        <h1 class="text-2xl font-bold text-gray-900">
            Akses Ditolak
        </h1>

        <p class="mt-3 text-sm text-gray-600">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>

        <a href="{{ route('dashboard') }}"
           class="mt-6 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>