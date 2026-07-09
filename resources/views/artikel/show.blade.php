<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} — Tax Center UIN SGD</title>
    <link rel="icon" href="{{ asset('images/TAXCENTER.png') }}" type="image/webp">
    <meta name="description" content="{{ $article->excerpt ?: Str::limit(strip_tags($article->body), 150) }}">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->excerpt ?: Str::limit(strip_tags($article->body), 150) }}">
    <meta property="og:image" content="{{ $article->cover_url }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anton&family=Lora:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1A3365;
            --gold: #FFBB00;
            --text: #1e293b;
            --muted: #64748b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --white: #ffffff;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #F4F6FA; color: var(--text); }

        /* ─── READING PROGRESS BAR ─── */
        #reading-progress {
            position: fixed; top: 0; left: 0; height: 3px; width: 0%;
            background: linear-gradient(90deg, var(--gold), #ff6b35);
            z-index: 9999; transition: width 0.1s linear;
        }

        /* ─── NAVBAR ─── */
        nav {
            background: var(--navy); padding: 0 32px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 500;
            box-shadow: 0 2px 20px rgba(0,0,0,0.25);
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-logo img { height: 32px; }
        .nav-brand { color: white; font-family: 'Anton', sans-serif; font-size: 16px; letter-spacing: 1px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a { color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; font-weight: 500; padding: 6px 12px; border-radius: 8px; transition: all 0.2s; }
        .nav-links a:hover { color: white; background: rgba(255,255,255,0.1); }
        .nav-links a.btn-gold { background: var(--gold); color: var(--navy); font-weight: 700; border-radius: 10px; }
        .nav-links a.btn-gold:hover { background: #e6a800; }

        /* ─── HERO ─── */
        .article-hero {
            position: relative; height: 460px; overflow: hidden; background: var(--navy);
        }
        .article-hero img { width: 100%; height: 100%; object-fit: cover; opacity: 0.45; }
        .article-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10,18,40,0.95) 0%, rgba(10,18,40,0.2) 60%, transparent 100%);
        }
        .hero-content {
            position: absolute; bottom: 0; left: 0; right: 0;
            max-width: 1160px; margin: 0 auto; padding: 40px 32px;
        }
        @media(max-width:900px) { .hero-content { padding: 24px 20px; } }

        .cat-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 99px; font-size: 11px; font-weight: 700;
            margin-bottom: 16px; letter-spacing: 0.05em; text-transform: uppercase;
        }
        .cat-berita     { background: rgba(26,51,101,0.9); color: white; border: 1px solid rgba(255,255,255,0.25); }
        .cat-edukasi    { background: rgba(5,150,105,0.9); color: white; }
        .cat-kebijakan  { background: rgba(124,58,237,0.9); color: white; }
        .cat-pengumuman { background: rgba(217,119,6,0.9); color: white; }

        .hero-title {
            font-family: 'Anton', sans-serif; font-size: clamp(26px, 4vw, 42px);
            color: white; line-height: 1.15; letter-spacing: 0.5px;
            margin-bottom: 16px; max-width: 760px; text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .hero-meta {
            display: flex; align-items: center; gap: 20px;
            color: rgba(255,255,255,0.55); font-size: 12.5px; flex-wrap: wrap;
        }
        .hero-meta span { display: flex; align-items: center; gap: 6px; }
        .hero-meta span i { color: var(--gold); }

        /* ─── BREADCRUMB ─── */
        .breadcrumb {
            max-width: 1160px; margin: 0 auto; padding: 18px 32px 0;
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--muted);
        }
        .breadcrumb a { color: var(--navy); text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 9px; }

        /* ─── LAYOUT GRID ─── */
        .page-layout {
            max-width: 1160px; margin: 0 auto;
            padding: 32px 32px 80px;
            display: grid;
            grid-template-columns: 40px 1fr 300px;
            gap: 32px;
            align-items: start;
        }
        @media(max-width:1024px) { 
            .page-layout { grid-template-columns: 1fr 280px; }
            .social-share-bar { display: none; }
        }
        @media(max-width:768px) {
            .page-layout { grid-template-columns: 1fr; padding: 20px 16px 60px; }
            .sidebar { display: none; }
        }

        /* ─── SOCIAL SHARE BAR (LEFT VERTICAL) ─── */
        .social-share-bar {
            position: sticky; top: 90px;
            display: flex; flex-direction: column; align-items: center; gap: 10px; padding-top: 8px;
        }
        .share-label {
            font-size: 9px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.12em;
            writing-mode: vertical-rl; text-orientation: mixed;
            margin-bottom: 4px; transform: rotate(180deg);
        }
        .share-icon-btn {
            width: 36px; height: 36px; border-radius: 50%; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; text-decoration: none; transition: all 0.2s;
        }
        .share-icon-btn:hover { transform: scale(1.12); }
        .si-wa    { background: #d1fae5; color: #065f46; }
        .si-wa:hover { background: #a7f3d0; }
        .si-tw    { background: #e0f2fe; color: #0369a1; }
        .si-tw:hover { background: #bae6fd; }
        .si-li    { background: #dbeafe; color: #1d4ed8; }
        .si-li:hover { background: #bfdbfe; }
        .si-copy  { background: #f1f5f9; color: #475569; }
        .si-copy:hover { background: #e2e8f0; }
        .divider-share { width: 1px; height: 24px; background: var(--border); }

        /* ─── ARTICLE CONTENT ─── */
        .article-content {
            background: var(--white); border-radius: 20px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .article-body {
            padding: 44px 48px;
            font-family: 'Lora', Georgia, serif;
            font-size: 17px; line-height: 1.9; color: #374151;
        }
        @media(max-width:600px) { .article-body { padding: 24px 20px; font-size: 15px; } }

        .article-body p { margin-bottom: 20px; }
        .article-body h2 {
            font-family: 'Anton', sans-serif; font-size: 24px; color: var(--navy);
            margin: 36px 0 14px; letter-spacing: 0.5px; line-height: 1.2;
        }
        .article-body h3 {
            font-family: 'Anton', sans-serif; font-size: 19px; color: #1e40af;
            margin: 28px 0 10px;
        }
        .article-body ul, .article-body ol { margin: 12px 0 20px 22px; }
        .article-body li { margin-bottom: 8px; }
        .article-body b, .article-body strong { color: var(--navy); font-weight: 700; }
        .article-body a { color: #1d4ed8; text-decoration: underline; text-decoration-color: rgba(29,78,216,0.3); }
        .article-body a:hover { text-decoration-color: #1d4ed8; }
        .article-body blockquote {
            border-left: 4px solid var(--gold); padding: 16px 24px;
            background: linear-gradient(135deg, #fefce8, #fffbeb);
            border-radius: 0 12px 12px 0; margin: 24px 0;
            font-style: italic; color: #92400e; font-size: 16px;
        }
        .article-body img { max-width: 100%; border-radius: 12px; margin: 20px 0; }

        /* Article footer bar */
        .article-footer-bar {
            border-top: 1px solid var(--border);
            padding: 24px 48px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }
        @media(max-width:600px) { .article-footer-bar { padding: 20px; } }

        .tag-area { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .tag-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .tag-chip {
            padding: 4px 12px; background: #f1f5f9; color: var(--navy);
            border-radius: 99px; font-size: 11px; font-weight: 600; text-decoration: none;
            transition: background 0.2s;
        }
        .tag-chip:hover { background: #dbeafe; }

        .share-btns-inline { display: flex; gap: 8px; }
        .share-btn-sm {
            padding: 7px 14px; border-radius: 10px; font-size: 12px; font-weight: 600;
            border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none; transition: all 0.2s;
        }
        .sb-wa   { background: #d1fae5; color: #065f46; }
        .sb-wa:hover { background: #a7f3d0; }
        .sb-copy { background: #f1f5f9; color: #475569; }
        .sb-copy:hover { background: #e2e8f0; }

        /* Author card */
        .author-card {
            margin: 0 48px 32px; padding: 20px 24px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 14px; display: flex; align-items: center; gap: 16px;
            border: 1px solid #bae6fd;
        }
        @media(max-width:600px) { .author-card { margin: 0 20px 24px; } }
        .author-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--navy); display: flex; align-items: center; justify-content: center;
            font-family: 'Anton', sans-serif; font-size: 18px; color: var(--gold); flex-shrink: 0;
        }
        .author-name { font-weight: 700; font-size: 13px; color: var(--navy); }
        .author-role { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* Back nav */
        .back-nav {
            padding: 20px 48px 32px;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--muted); font-size: 13px; font-weight: 500; text-decoration: none;
            padding: 8px 16px; border-radius: 10px; border: 1px solid var(--border);
            transition: all 0.2s; background: white;
        }
        .back-link:hover { background: var(--navy); color: white; border-color: var(--navy); }
        @media(max-width:600px) { .back-nav { padding: 20px; } }

        /* ─── SIDEBAR ─── */
        .sidebar { position: sticky; top: 90px; }
        .sidebar-section { margin-bottom: 24px; }

        .sidebar-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
        }
        .sidebar-header-line {
            width: 4px; height: 20px; background: var(--gold); border-radius: 2px; flex-shrink: 0;
        }
        .sidebar-header-text {
            font-size: 11px; font-weight: 800; color: var(--navy);
            text-transform: uppercase; letter-spacing: 0.12em;
        }

        /* Baca Juga Card */
        .baca-juga-item {
            display: flex; gap: 12px; padding: 14px 0;
            border-bottom: 1px solid var(--border); text-decoration: none; color: inherit;
            transition: all 0.2s; align-items: flex-start;
        }
        .baca-juga-item:last-child { border-bottom: none; padding-bottom: 0; }
        .baca-juga-item:first-child { padding-top: 0; }
        .baca-juga-thumb {
            width: 72px; height: 58px; object-fit: cover; border-radius: 8px;
            flex-shrink: 0; transition: transform 0.2s;
        }
        .baca-juga-item:hover .baca-juga-thumb { transform: scale(1.04); overflow: hidden; border-radius: 8px; }
        .baca-juga-info { flex: 1; min-width: 0; }
        .baca-juga-cat {
            font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 5px; display: block;
        }
        .cat-text-berita     { color: #1d4ed8; }
        .cat-text-edukasi    { color: #059669; }
        .cat-text-kebijakan  { color: #7c3aed; }
        .cat-text-pengumuman { color: #d97706; }
        .baca-juga-title {
            font-size: 12.5px; font-weight: 600; color: var(--text); line-height: 1.45;
            display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
            transition: color 0.2s;
        }
        .baca-juga-item:hover .baca-juga-title { color: var(--navy); }
        .baca-juga-date { font-size: 10px; color: #94a3b8; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

        /* About card */
        .about-card {
            background: linear-gradient(135deg, var(--navy), #1e40af);
            border-radius: 16px; padding: 24px; color: white;
        }
        .about-card-title { font-family: 'Anton', sans-serif; font-size: 16px; letter-spacing: 0.5px; margin-bottom: 10px; }
        .about-card-text { font-size: 12px; opacity: 0.75; line-height: 1.7; }
        .about-card-btn {
            display: inline-flex; align-items: center; gap: 6px; margin-top: 16px;
            background: var(--gold); color: var(--navy); font-weight: 700; font-size: 12px;
            padding: 9px 16px; border-radius: 10px; text-decoration: none; transition: all 0.2s;
        }
        .about-card-btn:hover { background: #e6a800; transform: translateY(-1px); }

        /* ─── FOOTER ─── */
        footer { background: var(--navy); color: rgba(255,255,255,0.4); text-align: center; padding: 28px; font-size: 12px; }
        footer a { color: var(--gold); text-decoration: none; }

        /* ─── COPY TOAST ─── */
        .toast {
            position: fixed; bottom: 28px; right: 28px;
            background: #1e293b; color: white; padding: 12px 20px;
            border-radius: 12px; font-size: 13px; font-weight: 500;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            transform: translateY(100px); opacity: 0;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 9999; display: flex; align-items: center; gap: 8px;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body>

<!-- Reading Progress Bar -->
<div id="reading-progress"></div>

<!-- Navbar -->
<nav>
    <a href="{{ route('login') }}" class="nav-logo">
        <img src="{{ asset('images/logo-tc.webp') }}" alt="Tax Center">
        <span class="nav-brand">TAX CENTER</span>
    </a>
    <div class="nav-links">
        <a href="{{ route('login') }}">Beranda</a>
        <a href="{{ route('artikel.index') }}" style="color:white;">Artikel</a>
        <a href="{{ route('login') }}" class="btn-gold">Masuk LMS</a>
    </div>
</nav>

<!-- Hero -->
<div class="article-hero">
    <img src="{{ $article->cover_url }}" alt="{{ $article->title }}" loading="eager">
    <div class="article-hero-overlay"></div>
    <div class="hero-content">
        @php
            $catClass = match($article->category) {
                'Berita'     => 'cat-berita',
                'Edukasi'    => 'cat-edukasi',
                'Kebijakan'  => 'cat-kebijakan',
                'Pengumuman' => 'cat-pengumuman',
                default      => 'cat-berita'
            };
            $readTime = max(1, ceil(str_word_count(strip_tags($article->body)) / 200));
        @endphp
        <span class="cat-badge {{ $catClass }}">
            <i class="fas fa-tag" style="font-size:9px;"></i> {{ $article->category }}
        </span>
        <h1 class="hero-title">{{ $article->title }}</h1>
        <div class="hero-meta">
            <span><i class="fas fa-user-circle"></i> {{ $article->author_name }}</span>
            <span><i class="far fa-calendar-alt"></i> {{ $article->published_at->isoFormat('D MMMM Y') }}</span>
            <span><i class="far fa-clock"></i> {{ $readTime }} menit baca</span>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="{{ route('login') }}"><i class="fas fa-home" style="font-size:11px;"></i> Beranda</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('artikel.index') }}">Artikel</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ Str::limit($article->title, 50) }}</span>
</div>

<!-- Main Page Layout -->
<div class="page-layout">

    <!-- COL 1: Social Share Bar (Vertical Left) -->
    <div class="social-share-bar">
        <span class="share-label">Bagikan</span>
        <div class="divider-share"></div>
        <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
           target="_blank" class="share-icon-btn si-wa" title="Bagikan ke WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->url()) }}"
           target="_blank" class="share-icon-btn si-tw" title="Bagikan ke Twitter">
            <i class="fab fa-x-twitter"></i>
        </a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($article->title) }}"
           target="_blank" class="share-icon-btn si-li" title="Bagikan ke LinkedIn">
            <i class="fab fa-linkedin-in"></i>
        </a>
        <button onclick="copyLink()" class="share-icon-btn si-copy" title="Salin Link" id="copyBtnSide">
            <i class="fas fa-link" id="copyIconSide"></i>
        </button>
    </div>

    <!-- COL 2: Article Content -->
    <div>
        <div class="article-content" id="article-content">
            <!-- Article Body -->
            <div class="article-body">
                {!! nl2br(e($article->body)) !!}
            </div>

            <!-- Author Card -->
            <div class="author-card">
                <div class="author-avatar">{{ strtoupper(substr($article->author_name, 0, 1)) }}</div>
                <div>
                    <div class="author-name">{{ $article->author_name }}</div>
                    <div class="author-role">Redaksi Tax Center UIN SGD Bandung</div>
                </div>
            </div>

            <!-- Footer Bar -->
            <div class="article-footer-bar">
                <div class="tag-area">
                    <span class="tag-label">Topik:</span>
                    <a href="{{ route('artikel.index', ['kategori' => $article->category]) }}" class="tag-chip">
                        <i class="fas fa-tag" style="font-size:9px;"></i> {{ $article->category }}
                    </a>
                    <a href="{{ route('artikel.index') }}" class="tag-chip">
                        Tax Center
                    </a>
                </div>
                <div class="share-btns-inline">
                    <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
                       target="_blank" class="share-btn-sm sb-wa">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <button onclick="copyLink()" class="share-btn-sm sb-copy" id="copyBtn">
                        <i class="fas fa-link"></i> Salin Link
                    </button>
                </div>
            </div>

            <!-- Back Link -->
            <div class="back-nav">
                <a href="{{ route('artikel.index') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Semua Artikel
                </a>
            </div>
        </div>
    </div>

    <!-- COL 3: Sidebar -->
    <aside class="sidebar">

        @if($related->count())
        <!-- Baca Juga Section -->
        <div class="sidebar-section">
            <div class="sidebar-header">
                <div class="sidebar-header-line"></div>
                <span class="sidebar-header-text">Baca Juga</span>
            </div>
            <div>
                @foreach($related as $rel)
                @php
                    $relCatText = match($rel->category) {
                        'Edukasi'    => 'cat-text-edukasi',
                        'Kebijakan'  => 'cat-text-kebijakan',
                        'Pengumuman' => 'cat-text-pengumuman',
                        default      => 'cat-text-berita'
                    };
                @endphp
                <a href="{{ route('artikel.show', $rel->slug) }}" class="baca-juga-item">
                    <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" class="baca-juga-thumb">
                    <div class="baca-juga-info">
                        <span class="baca-juga-cat {{ $relCatText }}">{{ $rel->category }}</span>
                        <div class="baca-juga-title">{{ $rel->title }}</div>
                        <div class="baca-juga-date">
                            <i class="far fa-calendar-alt" style="font-size:9px;"></i>
                            {{ $rel->published_at->isoFormat('D MMM Y') }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- About Card -->
        <div class="about-card">
            <div class="about-card-title">Tax Center UIN SGD</div>
            <p class="about-card-text">
                Pusat edukasi perpajakan terpadu yang berkomitmen mencetak profesional pajak berkualitas di bawah FISIP UIN Sunan Gunung Djati Bandung.
            </p>
            <a href="{{ route('login') }}" class="about-card-btn">
                <i class="fas fa-sign-in-alt"></i> Masuk LMS
            </a>
        </div>

    </aside>
</div>

<!-- Footer -->
<footer>
    <p>© {{ date('Y') }} Tax Center FISIP UIN Sunan Gunung Djati Bandung &nbsp;|&nbsp;
       <a href="{{ route('artikel.index') }}">← Semua Artikel</a> &nbsp;|&nbsp;
       <a href="{{ route('login') }}">Masuk LMS</a>
    </p>
</footer>

<!-- Copy Toast -->
<div class="toast" id="copyToast">
    <i class="fas fa-check-circle" style="color:#4ade80;"></i>
    Link berhasil disalin!
</div>

<script>
// ─── Reading Progress Bar ───
const progressBar = document.getElementById('reading-progress');
const articleEl = document.getElementById('article-content');

window.addEventListener('scroll', () => {
    if (!articleEl) return;
    const articleTop = articleEl.getBoundingClientRect().top + window.scrollY - 80;
    const articleBottom = articleTop + articleEl.offsetHeight;
    const scrolled = window.scrollY;
    const total = articleBottom - articleTop - window.innerHeight;
    const progress = Math.min(100, Math.max(0, ((scrolled - articleTop) / total) * 100));
    progressBar.style.width = progress + '%';
});

// ─── Copy Link ───
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        // Update all copy buttons
        const btn = document.getElementById('copyBtn');
        const btnSide = document.getElementById('copyBtnSide');
        const iconSide = document.getElementById('copyIconSide');

        if (btn) { btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!'; btn.style.background = '#d1fae5'; btn.style.color = '#065f46'; }
        if (iconSide) iconSide.className = 'fas fa-check';
        if (btnSide) btnSide.style.background = '#d1fae5';

        // Show toast
        const toast = document.getElementById('copyToast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2800);

        setTimeout(() => {
            if (btn) { btn.innerHTML = '<i class="fas fa-link"></i> Salin Link'; btn.style.background = ''; btn.style.color = ''; }
            if (iconSide) iconSide.className = 'fas fa-link';
            if (btnSide) btnSide.style.background = '';
        }, 2500);
    });
}
</script>
</body>
</html>