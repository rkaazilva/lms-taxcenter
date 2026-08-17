@php
    $role = strtoupper(session('role', 'SISWA'));
    $userName = session('nama', 'User');
    $userEmail = session('email', 'user@mail.com');

    if (in_array($role, ['ADMIN', 'ADMIN_LMS'])) {
        // Admin Theme (Bright Violet/Fuchsia)
        $themeLogoText = 'text-violet-600';
        $themeLogoBg = 'bg-white border border-violet-100/50 text-violet-600';
        $themeSidebarBg = 'bg-gradient-to-b from-violet-50 to-white border-r border-violet-100 shadow-[4px_0_24px_rgba(139,92,246,0.05)]';
        $themeSidebarBorder = 'border-violet-100';
        $themeMenuHover = 'hover:bg-violet-100/50 hover:text-violet-950';
        $themeMenuActive = 'bg-violet-600 text-white font-bold shadow-md shadow-violet-600/10';
        $themeMenuActiveIcon = 'text-white';
        $themeMenuDefault = 'text-violet-900/70';
        $themeTextMuted = 'text-violet-400/80';
        $themeBtnPrimary = 'bg-violet-600 hover:bg-violet-700 text-white';
        $themeNotifIcon = 'text-violet-600';
        $themeNotifIconClass = 'text-violet-600';
        $themeNotifBorder = 'hover:border-violet-200';
        $themeNotifHover = 'hover:bg-violet-50';
        $themeBadge = 'bg-violet-500 text-white';
        $themeLinkText = 'text-violet-600 hover:text-violet-800';
        $themeDotBg = 'bg-violet-600';
        $themeDotLight = 'bg-violet-50/20';
        $themeInitialBg = 'bg-violet-50 text-violet-700 border-violet-100';
    } elseif (in_array($role, ['TUTOR', 'GURU', 'DOSEN'])) {
        // Teacher Theme (Bright Emerald/Teal)
        $themeLogoText = 'text-emerald-600';
        $themeLogoBg = 'bg-white border border-emerald-100/50 text-emerald-600';
        $themeSidebarBg = 'bg-gradient-to-b from-emerald-50 to-white border-r border-emerald-100 shadow-[4px_0_24px_rgba(16,185,129,0.05)]';
        $themeSidebarBorder = 'border-emerald-100';
        $themeMenuHover = 'hover:bg-emerald-100/50 hover:text-emerald-950';
        $themeMenuActive = 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/10';
        $themeMenuActiveIcon = 'text-white';
        $themeMenuDefault = 'text-emerald-900/70';
        $themeTextMuted = 'text-emerald-400/80';
        $themeBtnPrimary = 'bg-emerald-600 hover:bg-emerald-700 text-white';
        $themeNotifIcon = 'text-emerald-600';
        $themeNotifIconClass = 'text-emerald-600';
        $themeNotifBorder = 'hover:border-emerald-200';
        $themeNotifHover = 'hover:bg-emerald-50';
        $themeBadge = 'bg-emerald-500 text-white';
        $themeLinkText = 'text-emerald-600 hover:text-emerald-800';
        $themeDotBg = 'bg-emerald-600';
        $themeDotLight = 'bg-emerald-50/20';
        $themeInitialBg = 'bg-emerald-50 text-emerald-700 border-emerald-100';
    } else {
        // Student Theme (Bright Ocean Blue)
        $themeLogoText = 'text-blue-600';
        $themeLogoBg = 'bg-white border border-blue-100/50 text-blue-600';
        $themeSidebarBg = 'bg-gradient-to-b from-blue-50 to-white border-r border-blue-100 shadow-[4px_0_24px_rgba(59,130,246,0.05)]';
        $themeSidebarBorder = 'border-blue-100';
        $themeMenuHover = 'hover:bg-blue-100/50 hover:text-blue-955';
        $themeMenuActive = 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/10';
        $themeMenuActiveIcon = 'text-white';
        $themeMenuDefault = 'text-blue-900/70';
        $themeTextMuted = 'text-blue-400/80';
        $themeBtnPrimary = 'bg-blue-600 hover:bg-blue-700 text-white';
        $themeNotifIcon = 'text-blue-600';
        $themeNotifIconClass = 'text-blue-600';
        $themeNotifBorder = 'hover:border-blue-200';
        $themeNotifHover = 'hover:bg-blue-50';
        $themeBadge = 'bg-blue-500 text-white';
        $themeLinkText = 'text-blue-600 hover:text-blue-800';
        $themeDotBg = 'bg-blue-600';
        $themeDotLight = 'bg-blue-50/20';
        $themeInitialBg = 'bg-blue-50 text-blue-700 border-blue-100';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Tax Center UIN SGD</title>
    <link rel="icon" href="{{ asset('images/TAXCENTER.png') }}" type="image/webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        blue: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            650: '#2563eb', // Premium vibrant blue
                            850: '#1e3a8a',
                            950: '#030712',
                        },
                        indigo: {
                            650: '#4f46e5',
                        },
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            650: '#059669',
                            755: '#047857',
                            850: '#064e3b',
                        },
                        violet: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            650: '#7c3aed',
                            850: '#5b21b6',
                        },
                        yellow: {
                            405: '#facc15',
                            450: '#eab308',
                        },
                        red: {
                            650: '#dc2626',
                        },
                        gray: {
                            55: '#f8fafc',
                            105: '#f1f5f9',
                            150: '#f3f4f6',
                            250: '#e5e7eb',
                            650: '#4b5563',
                            850: '#1f2937',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scroll-fade-container {
            position: relative;
            display: block;
            width: 100%;
        }
        .scroll-fade-container::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 40px;
            background: linear-gradient(to right, transparent, rgba(248, 250, 252, 1));
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">
    <div class="min-h-screen flex relative overflow-x-hidden">
        
        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebarOverlay" class="fixed inset-0 bg-black/55 z-40 hidden transition-opacity duration-300 opacity-0" onclick="toggleMobileSidebar()"></div>

        <!-- MOBILE SIDEBAR DRAWER -->
        <div id="mobileSidebar" class="fixed left-0 top-0 bottom-0 w-64 {{ $themeSidebarBg }} z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-lg">
            <div class="p-6 text-center border-b border-gray-100 flex-shrink-0">
                <div class="w-14 h-14 {{ $themeLogoBg }} rounded-2xl flex items-center justify-center mx-auto mb-3 font-black text-xl shadow-sm border border-gray-55">TC</div>
                <h2 class="font-bold text-md leading-tight text-gray-800">LMS Portal</h2>
                <p class="text-[9px] {{ $themeTextMuted }} uppercase tracking-widest font-black mt-1">
                    {{ $role }}
                </p>
            </div>
            
            <nav class="mt-6 px-4 space-y-2 flex-1 overflow-y-auto scrollbar-none" data-active-class="{{ $themeMenuActive }}" data-default-class="{{ $themeMenuDefault }} {{ $themeMenuHover }}">
                @if(in_array($role, ['SISWA', 'PESERTA']))
                    <!-- MENU SISWA MOBILE -->
                    <a href="{{ url('/siswa/dashboard?tab=overview') }}" data-tab="overview" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request()->is('siswa/dashboard') && (!request('tab') || request('tab') == 'overview') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Beranda
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=jadwal') }}" data-tab="jadwal" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'jadwal' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Jadwal Kelas
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=materi') }}" data-tab="materi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'materi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-book-open w-4 text-center text-xs"></i> Materi Pelatihan
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=tugas') }}" data-tab="tugas" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'tugas' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-tasks w-4 text-center text-xs"></i> Tugas & Ujian
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=nilai') }}" data-tab="nilai" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'nilai' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-award w-4 text-center text-xs"></i> Rekap Nilai
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=kehadiran') }}" data-tab="kehadiran" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'kehadiran' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=profil') }}" data-tab="profil" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'profil' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-cog w-4 text-center text-xs"></i> Pengaturan Akun
                    </a>
                @elseif(in_array($role, ['ADMIN_LMS', 'ADMIN']))
                    <!-- MENU ADMIN MOBILE -->
                    <a href="{{ route('admin-lms.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin-lms.index') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Dashboard
                    </a>
                    <a href="{{ route('admin-lms.jadwal.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/jadwal*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Manajemen Jadwal
                    </a>
                    <a href="{{ route('admin-lms.materi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/materi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-video w-4 text-center text-xs"></i> Manajemen Materi
                    </a>
                    <a href="{{ route('admin-lms.tugas.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/tugas*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-clipboard-list w-4 text-center text-xs"></i> Manajemen Tugas
                    </a>
                    <a href="{{ route('admin-lms.guru.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/guru*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-graduate w-4 text-center text-xs"></i> Manajemen Guru
                    </a>
                    <a href="{{ route('admin-lms.absensi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/absensi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran Siswa
                    </a>
                    <a href="{{ route('admin-lms.notifikasi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/notifikasi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-bullhorn w-4 text-center text-xs"></i> Kirim Notifikasi
                    </a>
                @else
                    <!-- MENU TUTOR MOBILE -->
                    <a href="{{ url('/guru/dashboard?tab=dashboard') }}" data-tab="dashboard" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request()->is('guru/dashboard') && (!request('tab') || request('tab') == 'dashboard') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Dashboard
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=materi') }}" data-tab="materi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'materi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-book w-4 text-center text-xs"></i> Materi
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=tugas') }}" data-tab="tugas" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'tugas' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-tasks w-4 text-center text-xs"></i> Tugas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=pengumuman') }}" data-tab="pengumuman" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'pengumuman' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-bullhorn w-4 text-center text-xs"></i> Pengumuman Kelas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=penilaian') }}" data-tab="penilaian" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'penilaian' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-star w-4 text-center text-xs"></i> Penilaian
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=rekap') }}" data-tab="rekap" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'rekap' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-table w-4 text-center text-xs"></i> Rekap Nilai Kelas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=absensi') }}" data-tab="absensi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'absensi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran Siswa
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=profil') }}" data-tab="profil" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'profil' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-cog w-4 text-center text-xs"></i> Pengaturan Akun
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-100 flex-shrink-0">
                <!-- Tombol Keluar -->
                <a href="{{ route('logout') }}" class="flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50 hover:text-red-650 transition font-bold text-xs">
                    <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i> Keluar
                </a>
            </div>
        </div>

        <!-- DESKTOP SIDEBAR -->
        <div class="w-64 {{ $themeSidebarBg }} flex-shrink-0 hidden md:flex flex-col fixed left-0 top-0 bottom-0 z-30">
            <div class="p-6 text-center border-b border-gray-100 flex-shrink-0">
                <div class="w-16 h-16 {{ $themeLogoBg }} rounded-2xl flex items-center justify-center mx-auto mb-3 font-black text-2xl shadow-sm border border-gray-55">TC</div>
                <h2 class="font-bold text-lg leading-tight text-gray-800">LMS Portal</h2>
                <p class="text-[10px] {{ $themeTextMuted }} uppercase tracking-widest font-black mt-1">
                    {{ $role }}
                </p>
            </div>
            
            <nav class="mt-6 px-4 space-y-2 flex-1 overflow-y-auto scrollbar-none" data-active-class="{{ $themeMenuActive }}" data-default-class="{{ $themeMenuDefault }} {{ $themeMenuHover }}">
                @if(in_array($role, ['SISWA', 'PESERTA']))
                    <!-- MENU SISWA DESKTOP -->
                    <a href="{{ url('/siswa/dashboard?tab=overview') }}" data-tab="overview" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request()->is('siswa/dashboard') && (!request('tab') || request('tab') == 'overview') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Beranda
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=jadwal') }}" data-tab="jadwal" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'jadwal' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Jadwal Kelas
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=materi') }}" data-tab="materi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'materi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-book-open w-4 text-center text-xs"></i> Materi Pelatihan
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=tugas') }}" data-tab="tugas" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'tugas' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-tasks w-4 text-center text-xs"></i> Tugas & Ujian
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=nilai') }}" data-tab="nilai" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'nilai' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-award w-4 text-center text-xs"></i> Rekap Nilai
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=kehadiran') }}" data-tab="kehadiran" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'kehadiran' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran
                    </a>
                    <a href="{{ url('/siswa/dashboard?tab=profil') }}" data-tab="profil" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'profil' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-cog w-4 text-center text-xs"></i> Pengaturan Akun
                    </a>
                @elseif(in_array($role, ['ADMIN_LMS', 'ADMIN']))
                    <!-- MENU ADMIN DESKTOP -->
                    <a href="{{ route('admin-lms.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('admin-lms.index') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Dashboard
                    </a>
                    <a href="{{ route('admin-lms.jadwal.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/jadwal*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Jadwal Kelas
                    </a>
                    <a href="{{ route('admin-lms.materi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/materi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-video w-4 text-center text-xs"></i> Materi Pelatihan
                    </a>
                    <a href="{{ route('admin-lms.tugas.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/tugas*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-clipboard-list w-4 text-center text-xs"></i> Manajemen Tugas
                    </a>
                    <a href="{{ route('admin-lms.guru.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/guru*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-graduate w-4 text-center text-xs"></i> Manajemen Guru
                    </a>
                    <a href="{{ route('admin-lms.absensi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/absensi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran Siswa
                    </a>
                    <a href="{{ route('admin-lms.notifikasi.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->is('admin-lms/notifikasi*') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-bullhorn w-4 text-center text-xs"></i> Kirim Notifikasi
                    </a>
                @else
                    <!-- MENU TUTOR DESKTOP -->
                    <a href="{{ url('/guru/dashboard?tab=dashboard') }}" data-tab="dashboard" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request()->is('guru/dashboard') && (!request('tab') || request('tab') == 'dashboard') ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-home w-4 text-center text-xs"></i> Dashboard
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=materi') }}" data-tab="materi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'materi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-book-open w-4 text-center text-xs"></i> Materi Pelatihan
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=tugas') }}" data-tab="tugas" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'tugas' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-tasks w-4 text-center text-xs"></i> Tugas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=pengumuman') }}" data-tab="pengumuman" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'pengumuman' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-bullhorn w-4 text-center text-xs"></i> Pengumuman Kelas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=penilaian') }}" data-tab="penilaian" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'penilaian' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-star w-4 text-center text-xs"></i> Penilaian
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=rekap') }}" data-tab="rekap" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'rekap' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-table w-4 text-center text-xs"></i> Rekap Nilai Kelas
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=absensi') }}" data-tab="absensi" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'absensi' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-check w-4 text-center text-xs"></i> Kehadiran Siswa
                    </a>
                    <a href="{{ url('/guru/dashboard?tab=profil') }}" data-tab="profil" class="sidebar-link flex items-center gap-3 p-3 rounded-xl {{ request('tab') == 'profil' ? $themeMenuActive : $themeMenuDefault . ' ' . $themeMenuHover }} transition font-bold text-xs">
                        <i class="fas fa-user-cog w-4 text-center text-xs"></i> Pengaturan Akun
                    </a>
                @endif
            </nav>
            
            <div class="p-4 border-t border-gray-100 flex-shrink-0">
                <!-- Tombol Keluar -->
                <a href="{{ route('logout') }}" class="flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50 hover:text-red-650 transition font-bold text-xs">
                    <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i> Keluar
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col min-w-0 md:ml-64">
            <!-- Header -->
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-6 md:px-8 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <!-- Burger icon for mobile drawer -->
                    <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-500 hover:text-indigo-900 transition focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-md md:text-lg font-bold text-gray-800">@yield('page_title')</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notification Bell and Dropdown -->
                    <div class="relative mr-1" id="notifDropdownWrapper">
                        <button onclick="toggleNotifDropdown()" class="relative w-10 h-10 bg-gray-50 {{ $themeNotifHover }} border border-gray-150 {{ $themeNotifBorder }} rounded-xl flex items-center justify-center text-gray-500 hover:{{ $themeNotifIcon }} transition duration-200 focus:outline-none shadow-sm">
                            <i class="fas fa-bell text-sm"></i>
                            <span id="notifBadge" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center hidden border border-white">0</span>
                        </button>
                        <!-- Dropdown Card -->
                        <div id="notifDropdown" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-150 z-50 hidden flex flex-col overflow-hidden max-h-[400px]">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h4 class="font-bold text-xs text-gray-800"><i class="fas fa-bell mr-1.5 {{ $themeNotifIconClass }}"></i> Notifikasi</h4>
                                <button onclick="markAllNotifRead()" class="text-[9px] {{ $themeLinkText }} font-bold transition">Tandai Dibaca Semua</button>
                            </div>
                            <div id="notifList" class="divide-y divide-gray-100 overflow-y-auto max-h-[300px]">
                                <div class="p-4 text-center text-gray-400 text-[10px] italic">Memuat notifikasi...</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-900 leading-none">{{ $userName }}</p>
                        <p class="text-[9px] text-gray-400 font-medium tracking-wide mt-1">{{ $userEmail }}</p>
                    </div>
                    <div class="w-10 h-10 {{ $themeInitialBg }} rounded-xl flex items-center justify-center font-bold text-sm shadow-sm transform hover:rotate-3 transition duration-200">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 md:p-8 flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="p-6 md:p-8 text-center text-gray-400 text-[10px] border-t border-gray-100/50">
                &copy; 2026 Tax Center UIN Sunan Gunung Djati Bandung. All Rights Reserved.
            </footer>
        </div>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.add('opacity-100'), 50);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        let isNotifOpen = false;

        function toggleNotifDropdown() {
            const dropdown = document.getElementById('notifDropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                isNotifOpen = true;
                loadNotifications();
            } else {
                dropdown.classList.add('hidden');
                isNotifOpen = false;
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notifDropdownWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notifDropdown').classList.add('hidden');
                isNotifOpen = false;
            }
        });

        function loadNotifications() {
            fetch("{{ route('api.notifications') }}")
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        updateNotifUI(res.data, res.unread_count);
                    }
                })
                .catch(err => console.error('Error fetching notifications:', err));
        }

        function updateNotifUI(data, unreadCount) {
            const badge = document.getElementById('notifBadge');
            if (unreadCount > 0) {
                badge.innerText = unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            const list = document.getElementById('notifList');
            list.innerHTML = '';

            if (data.length === 0) {
                list.innerHTML = '<div class="p-6 text-center text-gray-400 text-[10px] italic"><i class="fas fa-bell-slash text-lg mb-1.5 block"></i>Tidak ada notifikasi</div>';
                return;
            }

            data.forEach(item => {
                const isUnread = !item.is_read;
                const timeStr = formatNotifTime(item.created_at);
                const notifItem = document.createElement('a');
                notifItem.href = item.link ? item.link : '#';
                notifItem.className = `block p-3.5 text-left hover:bg-gray-50/70 transition decoration-none ${isUnread ? '{{ $themeDotLight }} font-bold' : ''}`;
                notifItem.innerHTML = `
                    <div class="flex justify-between items-start mb-0.5">
                        <span class="font-bold text-[11px] text-gray-800 leading-tight">${item.title}</span>
                        ${isUnread ? '<span class="w-1.5 h-1.5 rounded-full {{ $themeDotBg }} mt-1 flex-shrink-0"></span>' : ''}
                    </div>
                    <p class="text-[10px] text-gray-500 leading-relaxed m-0">${item.message}</p>
                    <span class="text-[8px] text-gray-400 mt-1.5 block font-normal">${timeStr}</span>
                `;
                list.appendChild(notifItem);
            });
        }

        function formatNotifTime(dateStr) {
            try {
                const date = new Date(dateStr);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / (1000 * 60));
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                
                if (diffMins < 1) return 'Baru saja';
                if (diffMins < 60) return diffMins + ' menit yang lalu';
                if (diffHours < 24) return diffHours + ' jam yang lalu';
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                return '';
            }
        }

        function markAllNotifRead() {
            fetch("{{ route('api.notifications.read') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    loadNotifications();
                }
            })
            .catch(err => console.error('Error marking notifications as read:', err));
        }

        // Poll for notifications unread count every 30 seconds
        setInterval(() => {
            fetch("{{ route('api.notifications') }}")
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        const badge = document.getElementById('notifBadge');
                        if (res.unread_count > 0) {
                            badge.innerText = res.unread_count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                });
        }, 30000);

        function updateSidebarActiveVisual(tabId) {
            document.querySelectorAll('nav[data-active-class]').forEach(nav => {
                const activeClasses = nav.getAttribute('data-active-class').split(' ').filter(c => c.trim() !== '');
                const defaultClasses = nav.getAttribute('data-default-class').split(' ').filter(c => c.trim() !== '');
                
                nav.querySelectorAll('.sidebar-link').forEach(link => {
                    const linkTab = link.getAttribute('data-tab');
                    if (linkTab === tabId) {
                        defaultClasses.forEach(c => link.classList.remove(c));
                        activeClasses.forEach(c => link.classList.add(c));
                    } else {
                        activeClasses.forEach(c => link.classList.remove(c));
                        defaultClasses.forEach(c => link.classList.add(c));
                    }
                });
            });
        }

        // Expose helper to window so that child templates can update sidebar visual
        window.syncSidebarActiveState = updateSidebarActiveVisual;
 
        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadNotifications();
 
            // Intercept sidebar link clicks to prevent page reloads
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    const tabId = this.getAttribute('data-tab');
                    if (tabId && typeof window.switchTab === 'function') {
                        e.preventDefault();
                        window.switchTab(tabId);
                        updateSidebarActiveVisual(tabId);
                        
                        // Close mobile sidebar if it's open
                        const sidebar = document.getElementById('mobileSidebar');
                        if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                            toggleMobileSidebar();
                        }
                    }
                });
            });

            // Show loading overlay on standard form submissions
            document.addEventListener('submit', function(e) {
                if (e.defaultPrevented) return;
                const form = e.target;
                if (form && !form.dataset.remote && form.id !== 'gradeForm' && form.id !== 'gradeBatchForm') {
                    const overlay = document.getElementById('loadingOverlay');
                    if (overlay) {
                        overlay.classList.remove('hidden');
                    }
                }
            });
        });
    </script>

    @php
        $spinnerColor = in_array($role, ['ADMIN', 'ADMIN_LMS']) ? 'border-t-violet-600' : (in_array($role, ['TUTOR', 'GURU', 'DOSEN']) ? 'border-t-emerald-600' : 'border-t-blue-600');
    @endphp
    <!-- Global Premium Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex flex-col items-center justify-center text-white">
        <div class="w-16 h-16 border-4 border-slate-200/30 {{ $spinnerColor }} rounded-full animate-spin mb-4"></div>
        <p class="font-bold text-sm" id="loadingText">Memproses data...</p>
        <p class="text-[11px] text-slate-200 mt-1">Harap tunggu, mohon jangan menyegarkan halaman atau menutup tab ini.</p>
    </div>

    <!-- Hidden element to force Tailwind CDN compilation of dynamic classes -->
    <div class="hidden bg-blue-600 bg-emerald-600 bg-violet-600 text-white shadow-blue-600/10 shadow-emerald-600/10 shadow-violet-600/10 hover:bg-blue-100/50 hover:bg-emerald-100/50 hover:bg-violet-100/50 hover:text-blue-955 hover:text-emerald-950 hover:text-violet-950 text-blue-900/70 text-emerald-900/70 text-violet-900/70"></div>
</body>
</html>