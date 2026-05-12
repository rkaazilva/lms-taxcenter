<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Tax Center UIN SGD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex">
        <!-- SIDEBAR -->
        <div class="w-64 bg-indigo-900 text-white flex-shrink-0 hidden md:block">
            <div class="p-6 text-center border-b border-indigo-800">
                <div class="w-16 h-16 bg-white text-indigo-900 rounded-2xl flex items-center justify-center mx-auto mb-3 font-bold text-2xl shadow-lg">TC</div>
                <h2 class="font-bold text-lg leading-tight">LMS Portal</h2>
                <p class="text-[10px] text-indigo-300 uppercase tracking-widest font-black mt-1">
                    {{ session('role', 'STUDENT') }}
                </p>
            </div>
            
            <nav class="mt-6 px-4 space-y-2">
                <!-- Gunakan request()->is() biar menu yang aktif otomatis berubah warnanya -->
                <a href="{{ url('/siswa/dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('siswa/dashboard') ? 'bg-indigo-800 text-white shadow-md' : 'text-indigo-300 hover:bg-indigo-800 hover:text-white' }} transition font-semibold">
                    <i class="fas fa-home w-5"></i> Dashboard
                </a>
                
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-indigo-300 hover:bg-indigo-800 hover:text-white transition font-semibold">
                    <i class="fas fa-book w-5"></i> Materi Kuliah
                </a>
                
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl text-indigo-300 hover:bg-indigo-800 hover:text-white transition font-semibold">
                    <i class="fas fa-tasks w-5"></i> Tugas & Ujian
                </a>

                <hr class="border-indigo-800 my-4 mx-2">

                <!-- Tombol Keluar: Pastiin route 'logout' atau 'login' sudah ada di web.php -->
                <form action="{{ route('login') }}" method="GET">
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition mt-10 font-bold">
                        <i class="fas fa-sign-out-alt w-5"></i> Keluar
                    </button>
                </form>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <!-- Icon burger buat mobile (opsional) -->
                    <i class="fas fa-bars text-gray-400 md:hidden cursor-pointer"></i>
                    <h1 class="text-xl font-bold text-gray-800">@yield('page_title')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ session('nama', 'User') }}</p>
                        <p class="text-[10px] text-gray-500 font-medium tracking-wide">{{ session('email', 'user@mail.com') }}</p>
                    </div>
                    <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-700 font-bold border-2 border-indigo-200 shadow-sm transform hover:rotate-3 transition">
                        {{ strtoupper(substr(session('nama', 'U'), 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                <!-- Bagian ini akan diisi oleh content dari dashboard.blade.php -->
                @yield('content')
            </main>

            <!-- Footer Sederhana -->
            <footer class="mt-auto p-8 text-center text-gray-400 text-xs">
                &copy; 2026 Tax Center UIN Sunan Gunung Djati Bandung
            </footer>
        </div>
    </div>
</body>
</html>