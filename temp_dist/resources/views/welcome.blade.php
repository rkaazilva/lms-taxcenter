<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Center UIN Sunan Gunung Djati Bandung</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/TAXCENTER.png') }}" type="image/webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#1A3365',
                        'navy-dark': '#0f1f3d',
                        'navy-light': '#243d75',
                        gold: '#FFBB00',
                        'gold-light': '#FFD04D',
                        'gold-dark': '#CC9500',
                        danger: '#FF4343',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Anton', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --navy: #1A3365;
            --navy-dark: #0f1f3d;
            --gold: #FFBB00;
            --gold-light: #FFD04D;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--navy); border-radius: 10px; }

        html, body { overflow-x: hidden; width: 100%; position: relative; }
        body.modal-active { overflow: hidden !important; }

        /* NAVBAR */
        .glass-nav {
            background: rgba(26, 51, 101, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 187, 0, 0.15);
        }

        /* DYNAMIC SHADER-LIKE BACKGROUND */
        .hero-dynamic-bg {
            position: absolute;
            inset: 0;
            background-color: #050a15;
            overflow: hidden;
            z-index: 0;
        }
        .mouse-spotlight {
            position: absolute;
            inset: 0;
            background: radial-gradient(800px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255,187,0,0.12), transparent 40%);
            z-index: 1;
            pointer-events: none;
        }
        .mouse-spotlight-2 {
            position: absolute;
            inset: 0;
            background: radial-gradient(600px circle at calc(100% - var(--mouse-x, 50%)) calc(100% - var(--mouse-y, 50%)), rgba(6,182,212,0.08), transparent 40%);
            z-index: 1;
            pointer-events: none;
        }
        .hero-blob {
            position: absolute;
            filter: blur(90px);
            opacity: 0.7;
            animation: float-blob 20s infinite alternate ease-in-out;
            border-radius: 50%;
        }
        .blob-1 {
            top: -10%; left: -10%;
            width: 50vw; height: 50vw;
            background: #1A3365;
            animation-delay: 0s;
        }
        .blob-2 {
            bottom: -20%; right: -10%;
            width: 60vw; height: 60vw;
            background: #0f1f3d;
            animation-delay: -5s;
        }
        .blob-3 {
            top: 20%; left: 30%;
            width: 45vw; height: 45vw;
            background: rgba(255, 187, 0, 0.15);
            animation-delay: -10s;
        }
        .blob-4 {
            bottom: 10%; left: 20%;
            width: 40vw; height: 40vw;
            background: rgba(6, 182, 212, 0.15);
            animation-delay: -15s;
        }
        @keyframes float-blob {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(8vw, 8vh) scale(1.1); }
            66% { transform: translate(-8vw, 4vh) scale(0.9); }
            100% { transform: translate(4vw, -8vh) scale(1); }
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .hero-content-wrapper {
            position: relative;
            z-index: 10;
        }

        /* GOLD SHIMMER TEXT */
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .shimmer-gold {
            background: linear-gradient(90deg, #FFBB00 0%, #FFE066 40%, #FFBB00 60%, #CC9500 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }

        /* STAT CARDS */
        .stat-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,187,0,0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            background: rgba(255,187,0,0.08);
            border-color: rgba(255,187,0,0.5);
            transform: translateY(-4px);
        }

        /* GOLD DIVIDER */
        .gold-divider {
            width: 60px; height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 2px;
        }

        /* PROGRAM CARDS */
        .program-card {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(26, 51, 101, 0.08);
        }
        .program-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--navy), var(--gold));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        .program-card:hover::before { transform: scaleX(1); }
        .program-card:hover {
            box-shadow: 0 20px 60px rgba(26,51,101,0.12);
            transform: translateY(-8px);
        }

        /* JADWAL TABLE */
        .jadwal-row {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .jadwal-row:hover {
            background: rgba(26, 51, 101, 0.03);
            border-left-color: var(--gold);
        }

        /* MODAL */
        .modal { transition: opacity 0.3s ease; }
        .modal-content { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }

        /* INPUT FOCUS */
        .auth-input:focus {
            border-color: rgba(255,187,0,0.6) !important;
            box-shadow: 0 0 0 3px rgba(255, 187, 0, 0.12);
            background: rgba(255,255,255,0.1) !important;
            outline: none;
        }

        /* PARTNER LOGOS */
        .partner-logo {
            filter: grayscale(100%) opacity(0.4);
            transition: all 0.3s ease;
        }
        .partner-logo:hover {
            filter: grayscale(0%) opacity(1);
            transform: scale(1.05);
        }

        /* BACKGROUND PATTERN */
        .pattern-bg {
            background-image: radial-gradient(rgba(26,51,101,0.05) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* SECTION LABEL */
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(26,51,101,0.06);
            border: 1px solid rgba(26,51,101,0.12);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--navy);
        }
        .section-label::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--gold);
            border-radius: 50%;
        }

        /* LIVE BADGE */
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,187,0,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(255,187,0,0); }
        }
        .live-dot { animation: pulse-gold 2s infinite; }

        /* NAV LINKS */
        .nav-link {
            position: relative;
            color: rgba(255,255,255,0.72);
            font-size: 13px; font-weight: 500;
            letter-spacing: 0.02em;
            transition: color 0.2s;
            padding-bottom: 2px;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0; right: 0;
            height: 1.5px;
            background: var(--gold);
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.3s ease;
        }
        .nav-link:hover { color: white; }
        .nav-link:hover::after { transform: scaleX(1); }

        /* FOOTER LINKS */
        .footer-link {
            color: rgba(255,255,255,0.45);
            font-size: 13px;
            transition: color 0.2s;
        }
        .footer-link:hover { color: var(--gold); }

        /* SHAKE ANIMATION */
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px,0,0); }
            20%, 80% { transform: translate3d(2px,0,0); }
            30%, 50%, 70% { transform: translate3d(-4px,0,0); }
            40%, 60% { transform: translate3d(4px,0,0); }
        }
        .shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

        /* SCROLL BOUNCE */
        @keyframes scroll-bounce {
            0%, 100% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(8px); opacity: 0.5; }
        }
        .scroll-indicator { animation: scroll-bounce 1.8s ease-in-out infinite; }

        /* FLOAT ANIMATION */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }


    </style>
</head>

<body class="font-sans text-gray-800 bg-white">

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="fixed w-full z-50 glass-nav">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="flex justify-between h-[72px] items-center">

            <!-- Logo Group -->
            <div class="flex items-center gap-4 cursor-pointer" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                <div class="flex items-center gap-3 flex-shrink-0">
                    <img src="{{ asset('images/logo-uin.webp') }}" alt="UIN SGD" class="h-10 w-auto object-contain flex-shrink-0" style="min-width:auto;">
                    <div class="w-px h-7 bg-white/20 flex-shrink-0"></div>
                    <img src="{{ asset('images/logo-tc.webp') }}" alt="Tax Center" class="h-9 w-auto object-contain flex-shrink-0" style="min-width:auto;">
                </div>
                <div class="hidden lg:flex flex-col ml-1">
                    <span class="font-display text-white text-[15px] leading-tight tracking-wide">TAX CENTER</span>
                    <span class="text-[9px] text-gold font-semibold tracking-[0.25em] uppercase">UIN Sunan Gunung Djati</span>
                </div>
            </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-7">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#tentang" class="nav-link">Tentang</a>
                <a href="#program" class="nav-link">Program</a>
                <a href="{{ route('artikel.index') }}" class="nav-link">Artikel</a>
                <button onclick="toggleModal('loginModal')"
                   class="text-[13px] font-semibold text-gold border border-gold/40 px-5 py-2 rounded-full hover:bg-gold hover:text-navy transition-all duration-300">
                    Masuk ke LMS
                </button>
                <a href="https://forms.gle/bcJJc1rbRRR8y6vX6" target="_blank"
                    class="flex items-center gap-2 bg-gold hover:bg-gold-light text-navy px-6 py-2.5 rounded-full font-bold text-[13px] transition-all duration-300 shadow-lg shadow-gold/20 hover:-translate-y-0.5">
                    <i class="fas fa-user-plus text-xs"></i>
                    Daftar Pelatihan
                </a>
            </div>

            <!-- Mobile Button -->
            <button class="md:hidden text-white text-xl" onclick="toggleMobileMenu()">
                <i class="fas fa-bars" id="menuIcon"></i>
            </button>
        </div>

        <!-- Mobile Dropdown -->
        <div id="mobileMenu" class="hidden md:hidden pb-5 border-t border-white/10 mt-2 pt-4 space-y-2">
            <a href="#beranda" class="block text-white/80 text-sm font-medium py-2 px-1" onclick="toggleMobileMenu()">Beranda</a>
            <a href="#tentang" class="block text-white/80 text-sm font-medium py-2 px-1" onclick="toggleMobileMenu()">Tentang</a>
            <a href="#program" class="block text-white/80 text-sm font-medium py-2 px-1" onclick="toggleMobileMenu()">Program</a>
            <a href="{{ route('artikel.index') }}" class="block text-white/80 text-sm font-medium py-2 px-1">Artikel</a>
            <div class="flex gap-3 pt-3">
                <button onclick="toggleModal('loginModal')"
                   class="flex-1 text-center text-[12px] font-semibold text-gold border border-gold/40 px-4 py-2.5 rounded-full hover:bg-gold hover:text-navy transition">
                    Masuk ke LMS
                </button>
                <a href="https://forms.gle/bcJJc1rbRRR8y6vX6" target="_blank"
                    class="flex-1 text-center bg-gold text-navy px-4 py-2.5 rounded-full font-bold text-[12px] transition block flex items-center justify-center">
                    Daftar Pelatihan
                </a>
            </div>
        </div>
    </div>
</nav>


<!-- ═══════════ HERO ═══════════ -->
<section id="beranda" class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Elements -->
    <div class="hero-dynamic-bg">
        <div class="mouse-spotlight"></div>
        <div class="mouse-spotlight-2"></div>
        <div id="interactive-blobs" class="absolute inset-0 w-full h-full" style="transition: transform 0.2s ease-out; will-change: transform;">
            <div class="hero-blob blob-1"></div>
            <div class="hero-blob blob-2"></div>
            <div class="hero-blob blob-3"></div>
            <div class="hero-blob blob-4"></div>
        </div>
    </div>

    <div class="relative hero-content-wrapper max-w-5xl mx-auto px-5 lg:px-8 py-32 w-full pt-40 flex flex-col items-center text-center">
        
        <div data-aos="fade-up" data-aos-duration="1000" class="flex flex-col items-center w-full">
            <div class="inline-flex items-center gap-3 mb-8 glass-panel rounded-full px-5 py-2">
                <span class="live-dot w-2 h-2 bg-gold rounded-full inline-block"></span>
                <span class="text-gold/90 text-[11px] md:text-[12px] font-bold tracking-[0.2em] uppercase text-center">Pendaftaran Batch 6 Dibuka!</span>
            </div>

            <h1 class="font-display text-[28px] sm:text-[40px] md:text-[60px] lg:text-[72px] leading-[1.15] text-white mb-6 md:mb-8 tracking-wide drop-shadow-2xl">
                LANGKAH PERTAMA MENJADI<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-gold to-gold-dark inline-block" style="animation: shimmer 4s linear infinite; background-size: 200% auto; padding-right: 2px;">PROFESIONAL PAJAK</span><br>
                <span class="text-white/90">ANDAL</span>
            </h1>

            <p class="text-blue-100/80 text-[15px] md:text-[17px] mb-12 leading-relaxed font-light max-w-3xl mx-auto">
                Platform e-learning eksklusif dari Tax Center UIN Sunan Gunung Djati Bandung untuk mengembangkan kompetensi perpajakan di era digital. Kuasai regulasi perpajakan terbaru, pelajari studi kasus nyata, hingga pahami sistem digital modern seperti Coretax langsung bersama para praktisi dan profesional berpengalaman. Tingkatkan nilai profesional dan daya saingmu melalui sertifikasi kompetensi resmi yang dikeluarkan oleh ATPI.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                <a href="https://forms.gle/bcJJc1rbRRR8y6vX6" target="_blank"
                    class="w-full sm:w-auto group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-gold to-gold-dark hover:from-gold-light hover:to-gold text-navy font-bold px-10 py-4 rounded-full text-[15px] transition-all duration-300 shadow-[0_8px_30px_rgba(255,187,0,0.4)] hover:shadow-[0_12px_40px_rgba(255,187,0,0.6)] hover:-translate-y-1">
                    <i class="fas fa-user-plus"></i>
                    Daftar Pelatihan
                </a>
                <a href="https://wa.me/6289637014638" target="_blank"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-3 text-white border border-white/20 hover:bg-white/10 px-10 py-4 rounded-full font-semibold text-[15px] transition-all glass-panel hover:-translate-y-1">
                    <i class="fab fa-whatsapp text-green-400 text-lg"></i>
                    Pusat Bantuan
                </a>
            </div>

            <!-- Stats Centered -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mt-16 max-w-4xl w-full">
                <div class="glass-panel rounded-3xl p-6 md:p-8 text-center transition-transform hover:-translate-y-2 flex flex-col items-center justify-center border-t border-gold/10 hover:border-gold/30 hover:shadow-[0_10px_40px_rgba(255,187,0,0.15)] group">
                    <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-gold text-2xl"></i>
                    </div>
                    <div class="font-display text-3xl md:text-4xl text-gold mb-3 leading-none tracking-wide">120+ LULUSAN</div>
                    <div class="text-white/80 text-[12px] md:text-[13px] leading-relaxed font-light">Telah bergabung dan siap bersaing di dunia kerja</div>
                </div>
                
                <div class="glass-panel rounded-3xl p-6 md:p-8 text-center transition-transform hover:-translate-y-2 flex flex-col items-center justify-center border-t border-gold/10 hover:border-gold/30 hover:shadow-[0_10px_40px_rgba(255,187,0,0.15)] group">
                    <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book-open text-gold text-2xl"></i>
                    </div>
                    <div class="font-display text-3xl md:text-4xl text-gold mb-3 leading-none tracking-wide">11 MATERI<br>PELATIHAN</div>
                    <div class="text-white/80 text-[12px] md:text-[13px] leading-relaxed font-light">Materi komprehensif dan aplikatif untuk kebutuhan profesional</div>
                </div>

                <div class="glass-panel rounded-3xl p-6 md:p-8 text-center transition-transform hover:-translate-y-2 flex flex-col items-center justify-center border-t border-gold/10 hover:border-gold/30 hover:shadow-[0_10px_40px_rgba(255,187,0,0.15)] group">
                    <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-medal text-gold text-2xl"></i>
                    </div>
                    <div class="text-white/90 text-[11px] font-bold tracking-[0.2em] mb-2">TERAFILIASI OLEH</div>
                    <div class="font-display text-4xl md:text-5xl text-gold mb-3 leading-none tracking-wider drop-shadow-lg">ATPI</div>
                    <div class="text-white/70 text-[11px] leading-relaxed italic">(Asosiasi Teknisi Perpajakan Indonesia)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20">
        <div class="flex flex-col items-center gap-2 scroll-indicator">
            <span class="text-white/40 text-[10px] tracking-[0.2em] uppercase font-semibold">Scroll</span>
            <div class="w-5 h-8 border-2 border-white/20 rounded-full flex items-start justify-center p-1">
                <div class="w-1.5 h-2 bg-gold rounded-full animate-bounce"></div>
            </div>
        </div>
    </div>

    <!-- Interactive Background Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const heroSection = document.getElementById('beranda');
            const blobContainer = document.getElementById('interactive-blobs');
            
            if (heroSection) {
                heroSection.addEventListener('mousemove', (e) => {
                    const rect = heroSection.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    heroSection.style.setProperty('--mouse-x', `${x}px`);
                    heroSection.style.setProperty('--mouse-y', `${y}px`);

                    if (blobContainer) {
                        const moveX = (e.clientX / window.innerWidth - 0.5) * 60; // Max 30px move
                        const moveY = (e.clientY / window.innerHeight - 0.5) * 60; // Max 30px move
                        blobContainer.style.transform = `translate(${moveX}px, ${moveY}px)`;
                    }
                });
            }
        });
    </script>
</section>


<!-- ═══════════ PARTNER STRIP ═══════════ -->
<div class="bg-white border-y border-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="flex flex-col items-center gap-5">
            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Didukung & Bermitra Dengan</span>
            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-14">
                <div class="flex flex-col items-center gap-2 group">
                    <img src="{{ asset('images/logo-uin.webp') }}" alt="UIN SGD" class="h-12 w-auto partner-logo">
                    <span class="text-[9px] text-gray-400 font-semibold tracking-widest uppercase group-hover:text-navy transition-colors">UIN SGD</span>
                </div>
                <div class="w-px h-12 bg-gray-100 hidden sm:block"></div>
                <div class="flex flex-col items-center gap-2 group">
                    <img src="{{ asset('images/logo-djp.webp') }}" alt="DJP" class="h-11 w-auto partner-logo">
                    <span class="text-[9px] text-gray-400 font-semibold tracking-widest uppercase group-hover:text-navy transition-colors">DJP</span>
                </div>
                <div class="w-px h-12 bg-gray-100 hidden sm:block"></div>
                <div class="flex flex-col items-center gap-2 group">
                    <img src="{{ asset('images/logo-atpi.webp') }}" alt="ATPI" class="h-11 w-auto partner-logo">
                    <span class="text-[9px] text-gray-400 font-semibold tracking-widest uppercase group-hover:text-navy transition-colors">ATPI</span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ═══════════ ARTIKEL TERBARU ═══════════ -->
@if(isset($articles) && $articles->count() > 0)
<section class="py-24 bg-white" id="artikel">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14" data-aos="fade-up">
            <div>
                <div class="section-label mb-5">Berita & Publikasi</div>
                <h2 class="font-display text-[40px] md:text-[50px] text-navy leading-tight tracking-wide">
                    ARTIKEL<br>
                    <span class="text-gold">TERBARU</span>
                </h2>
            </div>
            <a href="{{ route('artikel.index') }}"
               class="inline-flex items-center gap-2 text-navy border border-navy/20 hover:bg-navy hover:text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all">
                Lihat Semua Artikel <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($articles as $article)
            @php
                $catColors = [
                    'Berita'     => ['bg' => '#1A3365', 'text' => '#fff'],
                    'Edukasi'    => ['bg' => '#059669', 'text' => '#fff'],
                    'Kebijakan'  => ['bg' => '#7c3aed', 'text' => '#fff'],
                    'Pengumuman' => ['bg' => '#d97706', 'text' => '#fff'],
                ];
                $color = $catColors[$article->category] ?? ['bg' => '#64748b', 'text' => '#fff'];
            @endphp
            <a href="{{ route('artikel.show', $article->slug) }}"
               class="group block bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300"
               data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <!-- Cover -->
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ $article->cover_url }}" alt="{{ $article->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <span class="absolute top-3 left-3 text-[10px] font-bold px-3 py-1 rounded-full"
                          style="background:{{ $color['bg'] }}; color:{{ $color['text'] }};">
                        {{ $article->category }}
                    </span>
                </div>
                <!-- Content -->
                <div class="p-6">
                    <h3 class="font-bold text-navy text-[15px] leading-snug mb-2 line-clamp-2 group-hover:text-gold transition-colors">
                        {{ $article->title }}
                    </h3>
                    @if($article->excerpt)
                        <p class="text-gray-400 text-[12px] leading-relaxed line-clamp-2 mb-4">{{ $article->excerpt }}</p>
                    @endif
                    <div class="flex items-center gap-2 text-gray-400 text-[11px]">
                        <i class="far fa-calendar"></i>
                        <span>{{ $article->published_at->format('d M Y') }}</span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <i class="fas fa-user-circle"></i>
                        <span>{{ $article->author_name }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif


<!-- ═══════════ TENTANG KAMI ═══════════ -->
<section id="tentang" class="py-28 bg-white">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">

        <!-- Section Header — Centered -->
        <div class="text-center mb-16" data-aos="fade-up">
            <div class="section-label mb-5 inline-flex">Tentang Kami</div>
            <h2 class="font-display text-[36px] md:text-[56px] leading-tight text-navy tracking-wide">
                WADAH KOLABORASI<br>
                <span class="text-gold">& INOVASI</span> PERPAJAKAN
            </h2>
            <div class="gold-divider mx-auto mt-6 mb-0"></div>
        </div>

        <!-- Full-Width Image Banner with Overlay Stats -->
        <div class="relative rounded-3xl overflow-hidden shadow-2xl mb-16 flex flex-col justify-center" data-aos="fade-up" data-aos-delay="100" style="min-height: 400px;">
            <img src="{{ asset('images/kampus-fisip.webp') }}" alt="Gedung FISIP UIN SGD"
                 class="absolute inset-0 w-full h-full object-cover z-0">
            <!-- Dark gradient overlay -->
            <div class="absolute inset-0 z-10 bg-navy/80 md:bg-transparent" style="background: linear-gradient(to right, rgba(10,20,50,0.95) 0%, rgba(10,20,50,0.7) 50%, rgba(10,20,50,0.2) 100%);"></div>

            <!-- Floating content inside banner -->
            <div class="relative z-20 px-6 py-10 md:py-0 sm:px-10 md:px-16 max-w-3xl w-full">
                <div class="flex items-center gap-3 mb-4 md:mb-5">
                    <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center">
                        <i class="fas fa-handshake text-gold text-sm"></i>
                    </div>
                    <span class="text-gold text-[10px] sm:text-[11px] font-bold tracking-[0.25em] uppercase">Mitra Resmi DJP</span>
                </div>
                <p class="text-white/90 leading-relaxed text-[14px] sm:text-[15px] md:text-[16px] text-justify md:text-left">
                    Tax Center UIN Sunan Gunung Djati Bandung merupakan pusat edukasi, riset, dan pengembangan literasi perpajakan bagi mahasiswa maupun masyarakat umum yang berkolaborasi langsung dengan Direktorat Jenderal Pajak. Resmi dibentuk pada <strong class="text-gold">10 Desember 2020</strong>.
                </p>
                <!-- Mini stat strip inside banner -->
                <div class="flex flex-wrap gap-4 sm:gap-6 mt-6 md:mt-8">
                    <div>
                        <div class="text-gold font-display text-xl sm:text-2xl tracking-wide">2020</div>
                        <div class="text-white/60 text-[10px] sm:text-[11px] uppercase tracking-wider">Tahun Berdiri</div>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div>
                        <div class="text-gold font-display text-xl sm:text-2xl tracking-wide">120+</div>
                        <div class="text-white/60 text-[10px] sm:text-[11px] uppercase tracking-wider">Lulusan</div>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div>
                        <div class="text-gold font-display text-xl sm:text-2xl tracking-wide">ATPI</div>
                        <div class="text-white/60 text-[10px] sm:text-[11px] uppercase tracking-wider">Terafiliasi</div>
                    </div>
                </div>
            </div>

            <!-- Floating mini photo right side -->
            <div class="absolute bottom-6 right-6 w-44 rounded-2xl overflow-hidden shadow-xl border-4 border-white/20 hidden md:block">
                <img src="{{ asset('images/kampus-gedung.webp') }}" alt="Kampus UIN SGD"
                     class="w-full h-28 object-cover">
            </div>
        </div>

        <!-- 4 Feature Cards — equal grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-14" data-aos="fade-up" data-aos-delay="150">
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-navy/5 hover:shadow-md transition-all group border border-transparent hover:border-navy/10">
                <div class="w-12 h-12 bg-navy rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-certificate text-gold"></i>
                </div>
                <div class="font-bold text-sm text-navy mb-1">Terakreditasi</div>
                <div class="text-xs text-gray-400">Standar Nasional DJP</div>
            </div>
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-gold/5 hover:shadow-md transition-all group border border-transparent hover:border-gold/20">
                <div class="w-12 h-12 bg-gold rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-tie text-navy"></i>
                </div>
                <div class="font-bold text-sm text-navy mb-1">Expert Coach</div>
                <div class="text-xs text-gray-400">Praktisi Senior Pajak</div>
            </div>
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-navy/5 hover:shadow-md transition-all group border border-transparent hover:border-navy/10">
                <div class="w-12 h-12 bg-navy rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hands-helping text-gold"></i>
                </div>
                <div class="font-bold text-sm text-navy mb-1">Relawan Pajak</div>
                <div class="text-xs text-gray-400">Pengabdian Masyarakat</div>
            </div>
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-gold/5 hover:shadow-md transition-all group border border-transparent hover:border-gold/20">
                <div class="w-12 h-12 bg-gold rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-network-wired text-navy"></i>
                </div>
                <div class="font-bold text-sm text-navy mb-1">Jejaring Luas</div>
                <div class="text-xs text-gray-400">Alumni & Profesional</div>
            </div>
        </div>

        <!-- Divisi Utama — full width centered -->
        <div data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Divisi Utama Tax Center</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>
            <div class="grid grid-cols-3 gap-5 max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl p-6 flex flex-col items-center justify-center text-center border border-gray-100 hover:border-gold hover:shadow-lg transition-all group">
                    <div class="w-14 h-14 bg-navy/5 group-hover:bg-navy rounded-2xl flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-search text-navy group-hover:text-gold text-xl transition-colors"></i>
                    </div>
                    <div class="text-[13px] font-bold text-navy leading-tight">Riset &<br>Pengembangan</div>
                </div>
                <div class="bg-white rounded-2xl p-6 flex flex-col items-center justify-center text-center border border-gray-100 hover:border-gold hover:shadow-lg transition-all group">
                    <div class="w-14 h-14 bg-navy/5 group-hover:bg-navy rounded-2xl flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-users-cog text-navy group-hover:text-gold text-xl transition-colors"></i>
                    </div>
                    <div class="text-[13px] font-bold text-navy leading-tight">Hubungan<br>Masyarakat</div>
                </div>
                <div class="bg-white rounded-2xl p-6 flex flex-col items-center justify-center text-center border border-gray-100 hover:border-gold hover:shadow-lg transition-all group">
                    <div class="w-14 h-14 bg-navy/5 group-hover:bg-navy rounded-2xl flex items-center justify-center mb-3 transition-colors">
                        <i class="fas fa-bullhorn text-navy group-hover:text-gold text-xl transition-colors"></i>
                    </div>
                    <div class="text-[13px] font-bold text-navy leading-tight">Media &<br>Informasi</div>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- ═══════════ PROGRAM ═══════════ -->
<section id="program" class="py-24 overflow-hidden" style="background: #0f1f3d;">
    <style>
        /* PROGRAM CAROUSEL */
        .prog-scroll-wrap {
            overflow-x: auto;
            padding: 16px 32px 32px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            cursor: grab;
        }
        .prog-scroll-wrap:active { cursor: grabbing; }
        .prog-scroll-wrap::-webkit-scrollbar { display: none; }

        .prog-track {
            display: flex;
            gap: 24px;
            width: max-content;
        }

        /* Card themes */
        .prog-card {
            width: 300px;
            flex-shrink: 0;
            border-radius: 28px;
            padding: 36px 30px 32px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), box-shadow 0.35s ease;
        }
        .prog-card:hover { transform: translateY(-10px) scale(1.02); }

        /* Navy card */
        .prog-card.navy {
            background: #1A3365;
            box-shadow: 0 20px 60px rgba(10,18,40,0.5);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .prog-card.navy:hover { box-shadow: 0 30px 70px rgba(10,18,40,0.7), 0 0 30px rgba(255,187,0,0.12); }
        .prog-card.navy .prog-icon { background: rgba(255,187,0,0.12); color: #FFBB00; }
        .prog-card.navy .prog-title { color: #FFBB00; }
        .prog-card.navy .prog-divider { background: rgba(255,187,0,0.35); }
        .prog-card.navy .prog-desc { color: rgba(255,255,255,0.55); }
        .prog-card.navy .prog-tag { background: rgba(255,187,0,0.12); color: #FFBB00; }
        .prog-card.navy .prog-btn { background: rgba(255,187,0,0.15); color: #FFBB00; border: 1px solid rgba(255,187,0,0.3); }
        .prog-card.navy .prog-btn:hover { background: #FFBB00; color: #1A3365; }

        /* Gold card */
        .prog-card.gold {
            background: linear-gradient(140deg, #FFBB00 0%, #FFE066 100%);
            box-shadow: 0 20px 60px rgba(255,187,0,0.25);
            border: none;
        }
        .prog-card.gold:hover { box-shadow: 0 30px 70px rgba(255,187,0,0.4); }
        .prog-card.gold .prog-icon { background: rgba(26,51,101,0.12); color: #1A3365; }
        .prog-card.gold .prog-title { color: #1A3365; }
        .prog-card.gold .prog-divider { background: rgba(26,51,101,0.2); }
        .prog-card.gold .prog-desc { color: rgba(26,51,101,0.7); }
        .prog-card.gold .prog-tag { background: rgba(26,51,101,0.1); color: #1A3365; }
        .prog-card.gold .prog-btn { background: rgba(26,51,101,0.1); color: #1A3365; border: 1px solid rgba(26,51,101,0.2); }
        .prog-card.gold .prog-btn:hover { background: #1A3365; color: #FFBB00; }

        /* Glass card */
        .prog-card.glass {
            background: rgba(255,255,255,0.05);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
        }
        .prog-card.glass:hover { background: rgba(255,255,255,0.09); box-shadow: 0 30px 70px rgba(0,0,0,0.4); }
        .prog-card.glass .prog-icon { background: rgba(255,187,0,0.1); color: #FFBB00; }
        .prog-card.glass .prog-title { color: #fff; }
        .prog-card.glass .prog-divider { background: rgba(255,187,0,0.3); }
        .prog-card.glass .prog-desc { color: rgba(255,255,255,0.5); }
        .prog-card.glass .prog-tag { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); }
        .prog-card.glass .prog-btn { background: rgba(255,187,0,0.12); color: #FFBB00; border: 1px solid rgba(255,187,0,0.25); }
        .prog-card.glass .prog-btn:hover { background: #FFBB00; color: #1A3365; }

        .prog-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 24px;
        }
        .prog-title { font-size: 20px; font-weight: 800; letter-spacing: 0.04em; margin-bottom: 12px; text-transform: uppercase; line-height: 1.2; }
        .prog-divider { height: 3px; width: 48px; border-radius: 99px; margin-bottom: 14px; }
        .prog-desc { font-size: 13px; line-height: 1.7; margin-bottom: 20px; }
        .prog-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 24px; }
        .prog-tag { font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 99px; letter-spacing: 0.05em; }
        .prog-btn {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 700; padding: 10px 18px;
            border-radius: 12px; transition: all 0.25s; letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Decorative circle in card corner */
        .prog-card::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 140px; height: 140px;
            border-radius: 50%;
            opacity: 0.06;
        }
        .prog-card.navy::before, .prog-card.glass::before { background: #FFBB00; }
        .prog-card.gold::before { background: #1A3365; opacity: 0.1; }

        /* Scroll nav dots */
        .prog-dots { display: flex; gap: 8px; justify-content: center; margin-top: 28px; }
        .prog-dot { width: 8px; height: 8px; border-radius: 99px; background: rgba(255,255,255,0.2); cursor: pointer; transition: all 0.3s; }
        .prog-dot.active { width: 28px; background: #FFBB00; }

        /* PROGRAM MODAL */
        .prog-modal-bg {
            display: none;
            position: fixed; inset: 0; z-index: 80;
            background: rgba(10,18,40,0.85);
            backdrop-filter: blur(8px);
            align-items: center; justify-content: center;
            padding: 20px;
        }
        .prog-modal-bg.open { display: flex; }
        .prog-modal {
            background: #1A3365;
            border-radius: 28px;
            max-width: 520px; width: 100%;
            padding: 40px;
            position: relative;
            border: 1px solid rgba(255,187,0,0.15);
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
            animation: modalIn 0.35s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .prog-modal-close {
            position: absolute; top: 16px; right: 16px;
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: rgba(255,255,255,0.5);
            font-size: 14px; transition: all 0.2s;
        }
        .prog-modal-close:hover { background: rgba(255,255,255,0.15); color: #fff; }
    </style>

    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <!-- Heading -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12" data-aos="fade-up">
            <div>
                <div class="section-label mb-5" style="color:#FFBB00; border-color:rgba(255,187,0,0.3); background: rgba(255,187,0,0.08);">Program Unggulan</div>
                <h2 class="font-display text-[40px] md:text-[54px] leading-tight tracking-wide text-white">
                    PROGRAM <span style="color:#FFBB00">TERPILIH</span><br>
                    <span class="text-white/50 text-[24px] md:text-[32px] font-light tracking-normal">Kurikulum Berbasis Kompetensi</span>
                </h2>
            </div>
            <div class="flex items-center gap-3 text-white/40 text-sm mb-1">
                <i class="fas fa-hand-pointer text-gold/60"></i>
                <span>Klik kartu untuk detail</span>
            </div>
        </div>

        <!-- Scroll arrows -->
        <div class="relative">
            <button id="prog-prev" onclick="scrollProg(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 z-10 w-10 h-10 bg-white/10 hover:bg-gold text-white hover:text-navy rounded-full hidden md:flex items-center justify-center transition-all backdrop-blur-sm border border-white/10">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button id="prog-next" onclick="scrollProg(1)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 z-10 w-10 h-10 bg-white/10 hover:bg-gold text-white hover:text-navy rounded-full hidden md:flex items-center justify-center transition-all backdrop-blur-sm border border-white/10">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>

            <!-- Horizontal Scroll Track -->
            <div class="prog-scroll-wrap" id="progScroll">
                <div class="prog-track">

                    <!-- 1. Brevet Pajak A&B — Navy -->
                    <div class="prog-card navy" onclick="openProgModal(0)" data-aos="fade-right" data-aos-delay="100">
                        <div class="prog-icon"><i class="fas fa-scroll"></i></div>
                        <div class="prog-title">Brevet Pajak A&B</div>
                        <div class="prog-divider"></div>
                        <p class="prog-desc">Pelatihan intensif Brevet A & B dengan materi terkini sesuai UU Harmonisasi Peraturan Perpajakan.</p>
                        <div class="prog-tags">
                            <span class="prog-tag">PPh Pasal 21</span>
                            <span class="prog-tag">PPN</span>
                            <span class="prog-tag">PPh Badan</span>
                        </div>
                        <span class="prog-btn">Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>

                    <!-- 2. Uji Kompetensi CATT — Gold -->
                    <div class="prog-card gold" onclick="openProgModal(1)" data-aos="fade-right" data-aos-delay="200">
                        <div class="prog-icon"><i class="fas fa-laptop-code"></i></div>
                        <div class="prog-title">Uji Kompetensi CATT</div>
                        <div class="prog-divider"></div>
                        <p class="prog-desc">Sertifikasi kompetensi berbasis Computer Assisted Test for Taxation untuk mengukur keahlian teknis perpajakan.</p>
                        <div class="prog-tags">
                            <span class="prog-tag">Digital Test</span>
                            <span class="prog-tag">Sertifikasi</span>
                        </div>
                        <span class="prog-btn">Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>

                    <!-- 3. Seminar Perpajakan — Glass -->
                    <div class="prog-card glass" onclick="openProgModal(2)" data-aos="fade-right" data-aos-delay="300">
                        <div class="prog-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="prog-title">Seminar Perpajakan</div>
                        <div class="prog-divider"></div>
                        <p class="prog-desc">Diskusi bersama DJP dan Konsultan Pajak profesional mengenai isu fiskal terbaru dan kebijakan perpajakan terkini.</p>
                        <div class="prog-tags">
                            <span class="prog-tag">Update Fiskal</span>
                            <span class="prog-tag">Networking</span>
                        </div>
                        <span class="prog-btn">Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>

                    <!-- 4. Tax Goes to School — Navy -->
                    <div class="prog-card navy" onclick="openProgModal(3)" data-aos="fade-right" data-aos-delay="400">
                        <div class="prog-icon"><i class="fas fa-school"></i></div>
                        <div class="prog-title">Tax Goes to School</div>
                        <div class="prog-divider"></div>
                        <p class="prog-desc">Program edukasi perpajakan ke sekolah-sekolah untuk membangun kesadaran pajak sejak dini bagi generasi muda.</p>
                        <div class="prog-tags">
                            <span class="prog-tag">Edukasi</span>
                            <span class="prog-tag">Sosialisasi</span>
                        </div>
                        <span class="prog-btn">Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>

                    <!-- 5. Asistensi SPT Tahunan — Gold -->
                    <div class="prog-card gold" onclick="openProgModal(4)" data-aos="fade-right" data-aos-delay="500">
                        <div class="prog-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="prog-title">Asistensi SPT Tahunan</div>
                        <div class="prog-divider"></div>
                        <p class="prog-desc">Layanan pendampingan bagi wajib pajak dalam pengisian dan pelaporan SPT Tahunan melalui e-Filing secara gratis.</p>
                        <div class="prog-tags">
                            <span class="prog-tag">e-Filing</span>
                            <span class="prog-tag">Pelaporan</span>
                        </div>
                        <span class="prog-btn">Lihat Detail <i class="fas fa-arrow-right text-[10px]"></i></span>
                    </div>

                </div>
            </div>

            <!-- Dots -->
            <div class="prog-dots" id="progDots">
                <div class="prog-dot active" onclick="goToProgCard(0)"></div>
                <div class="prog-dot" onclick="goToProgCard(1)"></div>
                <div class="prog-dot" onclick="goToProgCard(2)"></div>
                <div class="prog-dot" onclick="goToProgCard(3)"></div>
                <div class="prog-dot" onclick="goToProgCard(4)"></div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════ PROGRAM MODAL ═══════════ -->
<div class="prog-modal-bg" id="progModalBg" onclick="closeProgModal(event)">
    <div class="prog-modal" id="progModalBox">
        <button class="prog-modal-close" onclick="closeProgModalBtn()"><i class="fas fa-times"></i></button>
        <div id="progModalContent"></div>
    </div>
</div>





<!-- ═══════════ CTA SECTION — FLOATING PHOTO COLLAGE ═══════════ -->
<section class="relative overflow-hidden" style="background-color: #0f1f3d; min-height: 700px;">

    <style>
        /* Floating card base */
        .photo-card {
            position: absolute;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 2px solid rgba(255,255,255,0.08);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            will-change: transform;
        }
        .photo-card:hover {
            box-shadow: 0 28px 80px rgba(0,0,0,0.65);
            transform: translateY(-6px) scale(1.02) !important;
            z-index: 20 !important;
        }
        .photo-card img { width: 100%; height: 100%; object-fit: cover; display: block; }

        /* Subtle parallax drift animations */
        @keyframes drift1 { 0%,100%{transform: rotate(-6deg) translateY(0px)} 50%{transform: rotate(-6deg) translateY(-10px)} }
        @keyframes drift2 { 0%,100%{transform: rotate(4deg) translateY(0px)} 50%{transform: rotate(4deg) translateY(-8px)} }
        @keyframes drift3 { 0%,100%{transform: rotate(-3deg) translateY(0px)} 50%{transform: rotate(-3deg) translateY(-12px)} }
        @keyframes drift4 { 0%,100%{transform: rotate(5deg) translateY(0px)} 50%{transform: rotate(5deg) translateY(-7px)} }

        .card-drift1 { animation: drift1 7s ease-in-out infinite; }
        .card-drift2 { animation: drift2 8s ease-in-out infinite 1s; }
        .card-drift3 { animation: drift3 9s ease-in-out infinite 0.5s; }
        .card-drift4 { animation: drift4 7.5s ease-in-out infinite 1.5s; }
    </style>

    <!-- Dark vignette edges -->
    <div class="absolute inset-0 pointer-events-none z-10"
         style="background: radial-gradient(ellipse at center, transparent 30%, rgba(10,18,40,0.7) 100%);"></div>

    <!-- Floating Photo Cards -->

    <!-- Card 1: Top-left, aerial wide -->
    <div class="photo-card card-drift1 hidden md:block"
         style="width:280px; height:175px; top:60px; left:5%; z-index:4; transform: rotate(-6deg);">
        <img src="{{ asset('images/kampus-aerial-wide.webp') }}" alt="Kampus Aerial">
    </div>

    <!-- Card 2: Top-right, FISIP building -->
    <div class="photo-card card-drift2 hidden md:block"
         style="width:260px; height:165px; top:40px; right:4%; z-index:4; transform: rotate(4deg);">
        <img src="{{ asset('images/kampus-fisip.webp') }}" alt="Gedung FISIP">
    </div>

    <!-- Card 3: Bottom-left, Gedung K -->
    <div class="photo-card card-drift3 hidden md:block"
         style="width:240px; height:160px; bottom:60px; left:7%; z-index:4; transform: rotate(-3deg);">
        <img src="{{ asset('images/kampus-gedung.webp') }}" alt="Gedung Kampus">
    </div>

    <!-- Card 4: Bottom-right, Gedung Keuangan Negara -->
    <div class="photo-card card-drift4 hidden md:block"
         style="width:270px; height:170px; bottom:50px; right:5%; z-index:4; transform: rotate(5deg);">
        <img src="{{ asset('images/kampus-keuangan.webp') }}" alt="Gedung Keuangan Negara">
    </div>

    <!-- CENTER CARD (hero card) — the main one with overlay text -->
    <div class="photo-card"
         style="width:90%; max-width:360px; height:auto; aspect-ratio:36/23; top:50%; left:50%; transform: translate(-50%,-50%); z-index:8; border-radius:22px; border: 2px solid rgba(255,187,0,0.2);">
        <img src="{{ asset('images/kampus-aerial.webp') }}" alt="Tax Center UIN SGD"
             style="filter: brightness(0.45);">
        <!-- Overlay text on center card -->
        <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding: 16px;">
            <span style="font-family:'Poppins',sans-serif; font-size:clamp(12px, 4vw, 16px); font-weight:600; color:white; line-height:1.2; letter-spacing:1px; text-transform:uppercase;">Kembangkan Kompetensi</span>
            <span style="font-family:'Anton',sans-serif; font-size:clamp(28px, 8vw, 38px); color:#FFBB00; line-height:1.1; letter-spacing:1.5px; text-shadow: 0 0 30px rgba(255,187,0,0.4); margin: 4px 0;">PERPAJAKANMU</span>
            <span style="font-family:'Poppins',sans-serif; font-size:clamp(12px, 4vw, 16px); font-weight:600; color:white; line-height:1.2; letter-spacing:1px; text-transform:uppercase;">Bersama Kami.</span>
        </div>
        <!-- Bottom label strip -->
        <div style="position:absolute; bottom:0; left:0; right:0; padding:8px 12px; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);">
            <span style="font-size:9px; color:rgba(255,255,255,0.5); font-weight:600; letter-spacing:0.15em; text-transform:uppercase;">Tax Center FISIP UIN Sunan Gunung Djati Bandung</span>
        </div>
    </div>

    <!-- Actual content sits below the collage, pushed down -->
    <div class="relative z-10 flex flex-col items-center justify-end pb-16 pt-[480px] md:pt-[520px] px-5 text-center mt-8">
        <div class="inline-block mb-5 px-4 py-1.5 rounded-full" style="background: rgba(255,187,0,0.12); border: 1px solid rgba(255,187,0,0.25);">
            <span style="color:#FFBB00; font-size:11px; font-weight:700; letter-spacing:0.2em; text-transform:uppercase;">Bergabung Sekarang</span>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 justify-center w-full sm:w-auto px-5 sm:px-0">
            <a href="https://forms.gle/bcJJc1rbRRR8y6vX6" target="_blank"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 font-bold px-10 py-4 rounded-2xl text-[15px] transition-all hover:-translate-y-1"
               style="background:#FFBB00; color:#1A3365; box-shadow: 0 8px 30px rgba(255,187,0,0.25);">
                <i class="fas fa-user-plus"></i>
                Daftar Pelatihan
            </a>
            <button onclick="toggleModal('loginModal')"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-3 font-semibold px-10 py-4 rounded-2xl text-[15px] transition-all text-white"
                style="border: 1px solid rgba(255,255,255,0.2);"
                onmouseover="this.style.background='rgba(255,255,255,0.08)'"
                onmouseout="this.style.background='transparent'">
                <i class="fas fa-sign-in-alt"></i>
                Sudah Punya Akun
            </button>
        </div>
    </div>
</section>


<!-- ═══════════ FOOTER ═══════════ -->
<footer style="background-color: #0f1f3d;" class="pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
        <div class="grid md:grid-cols-3 gap-12 mb-12 pb-12 border-b border-white/8">
            <div>
                <div class="flex items-center gap-3 mb-5">
                    <img src="{{ asset('images/logo-tc.webp') }}" alt="Tax Center"
                         class="h-12 w-auto object-contain brightness-0 invert opacity-80">
                    <div class="hidden" id="tc-text-fallback">
                        <span class="font-display text-white text-xl tracking-wider">TAX CENTER</span>
                    </div>
                </div>
                <p class="text-white/35 text-[13px] leading-relaxed">
                    Pusat edukasi dan riset perpajakan Universitas Islam Negeri Sunan Gunung Djati Bandung.
                </p>
                <p class="text-gold/40 text-[11px] italic font-medium mt-5">"Spirit of Tax Professionals"</p>
            </div>

            <div>
                <div class="text-gold text-[10px] font-bold tracking-[0.25em] uppercase mb-5">Navigasi</div>
                <div class="space-y-3">
                    <a href="#beranda" class="footer-link block">Beranda</a>
                    <a href="#tentang" class="footer-link block">Tentang Kami</a>
                    <a href="#program" class="footer-link block">Program</a>
                    <a href="https://forms.gle/bcJJc1rbRRR8y6vX6" target="_blank" class="footer-link block">Daftar Member</a>
                </div>
            </div>

            <div>
                <div class="text-gold text-[10px] font-bold tracking-[0.25em] uppercase mb-5">Temukan Kami</div>
                <div class="space-y-3 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-gold text-xs mt-1 flex-shrink-0"></i>
                        <span class="text-white/35 text-[13px] leading-relaxed">Jl. A.H. Nasution No.105, Cipadung, Cibiru, Kota Bandung</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="https://www.instagram.com/taxcenter_uinbdg?igsh=MWoydWQ2dDd1OWF6MQ==" target="_blank" class="w-9 h-9 rounded-xl bg-white/6 hover:bg-gold flex items-center justify-center text-white/35 hover:text-navy transition-all">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="https://www.tiktok.com/@taxcenter_uinbdg?_r=1&_t=ZS-96EuKxnd0tz" target="_blank" class="w-9 h-9 rounded-xl bg-white/6 hover:bg-gold flex items-center justify-center text-white/35 hover:text-navy transition-all">
                        <i class="fab fa-tiktok text-sm"></i>
                    </a>
                    <a href="https://youtube.com/@taxcentreapuinsunangunungd3204?si=Bm7mXIsgNfMGP6Jx" target="_blank" class="w-9 h-9 rounded-xl bg-white/6 hover:bg-gold flex items-center justify-center text-white/35 hover:text-navy transition-all">
                        <i class="fab fa-youtube text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <img src="/images/logo-uin.webp" alt="UIN" class="h-8 w-auto brightness-0 invert opacity-20"
                     onerror="this.style.display='none'">
                <div class="w-px h-6 bg-white/10"></div>
                <img src="/images/logo-tc.webp" alt="Tax Center" class="h-8 w-auto brightness-0 invert opacity-20"
                     onerror="this.style.display='none'">
                <div class="w-px h-6 bg-white/10"></div>
                <img src="/images/logo-djp.webp" alt="DJP" class="h-7 w-auto brightness-0 invert opacity-20"
                     onerror="this.style.display='none'">
                <div class="w-px h-6 bg-white/10"></div>
                <img src="/images/logo-atpi.webp" alt="ATPI" class="h-7 w-auto brightness-0 invert opacity-20"
                     onerror="this.style.display='none'">
            </div>
            <p class="text-white/20 text-[11px] tracking-wider uppercase text-center">
                &copy; 2026 Tax Center UIN SGD Bandung — All Rights Reserved
            </p>
        </div>
    </div>
</footer>


<!-- ═══════════ LOGIN MODAL ═══════════ -->
<div id="loginModal" class="modal opacity-0 pointer-events-none fixed inset-0 z-[60] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-navy-dark/95 backdrop-blur-md" onclick="toggleModal('loginModal')"></div>

    <div id="modalContent" class="modal-content relative z-10 w-full max-w-[420px] scale-95 opacity-0">

        <button onclick="toggleModal('loginModal')"
            class="absolute -top-4 -right-4 w-9 h-9 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white/50 hover:text-white transition z-20">
            <i class="fas fa-times text-sm"></i>
        </button>

        <!-- Gold top bar -->
        <div class="h-1.5 rounded-t-[28px]" style="background: linear-gradient(90deg, #FFBB00, #FFE066, #FFBB00);"></div>

        <div class="rounded-b-[28px] overflow-hidden" style="background: #1A3365;">
            <!-- Header -->
            <div class="px-8 pt-8 pb-6 text-center relative">
                <div class="absolute inset-0 opacity-10"
                     style="background: radial-gradient(circle at 50% 0%, #FFBB00 0%, transparent 65%);"></div>
                <div class="relative">
                    <div class="flex items-center justify-center gap-4 mb-5">
                        <img src="{{ asset('images/logo-uin.webp') }}" alt="UIN"
                             class="h-9 w-auto object-contain brightness-0 invert opacity-75">
                        <div class="w-px h-8 bg-white/15"></div>
                        <img src="{{ asset('images/logo-tc.webp') }}" alt="Tax Center"
                             class="h-10 w-auto object-contain brightness-0 invert opacity-75">
                    </div>
                    <h3 class="font-display text-[24px] text-white tracking-wide">PORTAL LMS</h3>
                    <p class="text-gold/60 text-[10px] font-bold tracking-[0.3em] uppercase mt-1">Member Only Access</p>
                </div>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                @if(session('error'))
                <div class="mb-5 p-4 rounded-xl flex items-center gap-3 shake"
                     style="background: rgba(255,67,67,0.1); border: 1px solid rgba(255,67,67,0.3);">
                    <i class="fas fa-exclamation-circle text-sm flex-shrink-0" style="color: #FF4343;"></i>
                    <span class="text-sm font-medium" style="color: #FF4343;">{{ session('error') }}</span>
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" onsubmit="handleLoginSubmit(event, this)" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold mb-2 uppercase tracking-[0.15em]" style="color: rgba(255,255,255,0.4);">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-sm pointer-events-none" style="color: rgba(255,255,255,0.2);"></i>
                            <input type="email" name="email" placeholder="email@member.com"
                                   value="{{ old('email') }}" required
                                   class="auth-input w-full pl-11 pr-4 py-4 rounded-xl text-[14px] font-medium transition-all duration-200"
                                   style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold mb-2 uppercase tracking-[0.15em]" style="color: rgba(255,255,255,0.4);">
                            Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-sm pointer-events-none" style="color: rgba(255,255,255,0.2);"></i>
                            <input type="password" name="password" id="passwordInput"
                                   placeholder="••••••••" required
                                   class="auth-input w-full pl-11 pr-12 py-4 rounded-xl text-[14px] font-medium transition-all duration-200"
                                   style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: white;">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 transition"
                                style="color: rgba(255,255,255,0.25);" onmouseover="this.style.color='rgba(255,255,255,0.6)'" onmouseout="this.style.color='rgba(255,255,255,0.25)'">
                                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit"
                        class="group w-full font-bold py-4 rounded-xl text-[14px] transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-3 mt-2"
                        style="background: #FFBB00; color: #1A3365;">
                        <span>MASUK SEKARANG</span>
                        <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </button>
                </form>

                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px" style="background: rgba(255,255,255,0.08);"></div>
                    <span class="text-[11px]" style="color: rgba(255,255,255,0.2);">atau</span>
                    <div class="flex-1 h-px" style="background: rgba(255,255,255,0.08);"></div>
                </div>

                <p class="text-center text-[12px]" style="color: rgba(255,255,255,0.3);">
                    Belum punya akun atau lupa password?
                    <a href="https://wa.me/6289637014638" target="_blank"
                       class="font-semibold ml-1 transition" style="color: rgba(255,187,0,0.7);"
                       onmouseover="this.style.color='#FFBB00'" onmouseout="this.style.color='rgba(255,187,0,0.7)'">
                        Pusat Bantuan
                    </a>
                </p>
            </div>
        </div>

        <div class="mt-4 text-center">
            <span class="text-[10px] flex items-center justify-center gap-1.5" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-shield-alt text-[9px]" style="color: rgba(255,187,0,0.35);"></i>
                Koneksi aman & terenkripsi
            </span>
        </div>
    </div>
</div>


<!-- ═══════════ SCRIPTS ═══════════ -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 900, offset: 80, easing: 'ease-out-cubic' });

    // Modal
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        const content = document.getElementById('modalContent');
        const isHidden = modal.classList.contains('opacity-0');
        modal.classList.toggle('opacity-0');
        modal.classList.toggle('pointer-events-none');
        document.body.classList.toggle('modal-active');
        if (isHidden) {
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
        }
    }

    function handleLoginSubmit(e, form) {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.style.cursor = 'not-allowed';
            btn.innerHTML = `<i class="fas fa-spinner fa-spin text-sm"></i><span>MEMPROSES LOGIN...</span>`;
        }
    }

    // Auto-open modal if Laravel session has error
    @if(session('error'))
        window.addEventListener('DOMContentLoaded', () => toggleModal('loginModal'));
    @endif

    // Mobile menu
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const icon = document.getElementById('menuIcon');
        menu.classList.toggle('hidden');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    }

    // Close mobile menu on outside click
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('mobileMenu');
        const btn = document.querySelector('button[onclick="toggleMobileMenu()"]');
        if (menu && !menu.classList.contains('hidden')) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                toggleMobileMenu();
            }
        }
    });

    // Password toggle
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Smooth scroll (offset for fixed navbar)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const top = target.getBoundingClientRect().top + window.scrollY - 80;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

    // Navbar shadow on scroll
    const nav = document.querySelector('nav');
    window.addEventListener('scroll', () => {
        nav.style.boxShadow = window.scrollY > 20
            ? '0 4px 30px rgba(10,20,50,0.5)'
            : 'none';
    });

    // Parallax effect for mockup
    const mockup = document.getElementById('heroMockup');
    if (mockup) {
        document.addEventListener('mousemove', (e) => {
            if (window.innerWidth >= 1024) {
                const x = (window.innerWidth / 2 - e.pageX) / 45;
                const y = (window.innerHeight / 2 - e.pageY) / 45;
                mockup.style.transform = `rotateY(${x}deg) rotateX(${y}deg)`;
            }
        });
        document.addEventListener('mouseleave', () => {
            if (window.innerWidth >= 1024) {
                mockup.style.transform = `rotateY(0deg) rotateX(0deg)`;
            }
        });
    }

    // ── Program Carousel ──
    const progData = [
        {
            icon: 'fa-scroll', title: 'Brevet Pajak A&B',
            desc: 'Program pelatihan intensif Brevet A & B dengan kurikulum berbasis kompetensi, mencakup PPh Orang Pribadi, PPh Badan, PPN, PPnBM, dan Pajak Daerah. Materi disesuaikan dengan UU Harmonisasi Peraturan Perpajakan terbaru. Lulusan mendapatkan sertifikat yang diakui secara nasional.',
            tags: ['PPh Pasal 21', 'PPN', 'PPh Badan', 'Pajak Daerah'], cta: 'Daftar Brevet',
            color: '#FFBB00'
        },
        {
            icon: 'fa-laptop-code', title: 'Uji Kompetensi CATT',
            desc: 'Sertifikasi kompetensi berbasis Computer Assisted Test for Taxation (CATT) untuk mengukur keahlian teknis perpajakan secara akurat dan objektif. Ujian dilakukan secara digital dan hasilnya dapat digunakan sebagai bukti kompetensi profesional.',
            tags: ['Digital Test', 'Sertifikasi', 'ATPI'], cta: 'Info Uji Kompetensi',
            color: '#FFBB00'
        },
        {
            icon: 'fa-chalkboard-teacher', title: 'Seminar Perpajakan',
            desc: 'Diskusi bulanan bersama pejabat DJP, Konsultan Pajak, dan akademisi untuk membahas isu fiskal, kebijakan perpajakan terbaru, dan implementasi sistem digital perpajakan. Peserta mendapatkan sertifikat seminar dan akses materi eksklusif.',
            tags: ['Update Fiskal', 'Networking', 'Webinar'], cta: 'Lihat Jadwal Seminar',
            color: '#FFBB00'
        },
        {
            icon: 'fa-school', title: 'Tax Goes to School',
            desc: 'Program edukasi perpajakan ke sekolah-sekolah menengah atas di wilayah Bandung Raya. Bertujuan membangun kesadaran dan pemahaman pajak sejak dini bagi generasi muda, dikemas dengan cara yang interaktif dan menarik.',
            tags: ['Edukasi', 'Sosialisasi', 'Kesadaran Pajak'], cta: 'Undang ke Sekolah',
            color: '#FFBB00'
        },
        {
            icon: 'fa-file-invoice-dollar', title: 'Asistensi SPT Tahunan',
            desc: 'Layanan pendampingan gratis bagi wajib pajak orang pribadi dalam pengisian dan pelaporan SPT Tahunan melalui e-Filing. Dilayani oleh Relawan Pajak terlatih yang bekerja sama dengan KPP setempat setiap musim pelaporan pajak.',
            tags: ['e-Filing', 'Pelaporan', 'Gratis', 'Relawan Pajak'], cta: 'Jadwalkan Asistensi',
            color: '#FFBB00'
        }
    ];

    function openProgModal(i) {
        const d = progData[i];
        document.getElementById('progModalContent').innerHTML = `
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl" style="background:rgba(255,187,0,0.12); color:#FFBB00">
                    <i class="fas ${d.icon}"></i>
                </div>
                <div>
                    <div class="text-gold/60 text-[10px] font-bold tracking-widest uppercase mb-1">Program Tax Center</div>
                    <h3 class="font-display text-2xl text-white tracking-wide uppercase">${d.title}</h3>
                </div>
            </div>
            <div class="w-12 h-1 bg-gold/40 rounded-full mb-5"></div>
            <p class="text-white/60 text-sm leading-relaxed mb-6">${d.desc}</p>
            <div class="flex flex-wrap gap-2 mb-7">${d.tags.map(t => `<span class="text-[10px] bg-gold/10 text-gold font-bold px-3 py-1.5 rounded-full border border-gold/20">${t}</span>`).join('')}</div>
            <a href="https://wa.me/6289637014638" target="_blank" class="inline-flex items-center gap-3 bg-gold hover:bg-yellow-300 text-navy font-bold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5">
                <i class="fab fa-whatsapp"></i> ${d.cta}
            </a>
        `;
        document.getElementById('progModalBg').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeProgModal(e) {
        if (e.target === document.getElementById('progModalBg')) closeProgModalBtn();
    }
    function closeProgModalBtn() {
        document.getElementById('progModalBg').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProgModalBtn(); });

    // Carousel scroll
    const progScroll = document.getElementById('progScroll');
    const cardWidth = 324; // card width + gap
    function scrollProg(dir) {
        progScroll.scrollBy({ left: dir * cardWidth, behavior: 'smooth' });
    }
    function goToProgCard(i) {
        progScroll.scrollTo({ left: i * cardWidth, behavior: 'smooth' });
    }
    // Update dots on scroll
    if (progScroll) {
        progScroll.addEventListener('scroll', () => {
            const idx = Math.round(progScroll.scrollLeft / cardWidth);
            document.querySelectorAll('.prog-dot').forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
        });
        // Drag to scroll
        let isDown = false, startX, scrollLeft;
        progScroll.addEventListener('mousedown', e => { isDown = true; startX = e.pageX - progScroll.offsetLeft; scrollLeft = progScroll.scrollLeft; });
        progScroll.addEventListener('mouseleave', () => { isDown = false; });
        progScroll.addEventListener('mouseup', () => { isDown = false; });
        progScroll.addEventListener('mousemove', e => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - progScroll.offsetLeft;
            progScroll.scrollLeft = scrollLeft - (x - startX) * 1.5;
        });
    }
</script>
</body>
</html>