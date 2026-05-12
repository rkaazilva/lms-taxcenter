<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel & Berita — Tax Center UIN SGD Bandung</title>
    <meta name="description" content="Kumpulan artikel, berita, dan edukasi perpajakan dari Tax Center UIN Sunan Gunung Djati Bandung.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #F4F6FA; color: #1e293b; }

        /* Navbar */
        nav { background: #1A3365; padding: 0 32px; height: 64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:50; box-shadow:0 2px 20px rgba(0,0,0,0.2); }
        .nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-logo img { height:32px; }
        .nav-brand { color:white; font-family:'Anton',sans-serif; font-size:16px; letter-spacing:1px; }
        .nav-links { display:flex; align-items:center; gap:8px; }
        .nav-links a { color:rgba(255,255,255,0.7); text-decoration:none; font-size:13px; font-weight:500; padding:6px 12px; border-radius:8px; transition:all 0.2s; }
        .nav-links a:hover { color:white; background:rgba(255,255,255,0.08); }
        .nav-links a.btn-gold { background:#FFBB00; color:#1A3365; font-weight:700; padding:8px 16px; border-radius:10px; }

        /* Hero */
        .hero { background: linear-gradient(135deg, #0f1f3d 0%, #1A3365 100%); padding: 80px 32px 60px; text-align:center; }
        .hero .label { display:inline-block; background:rgba(255,187,0,0.15); border:1px solid rgba(255,187,0,0.3); color:#FFBB00; font-size:11px; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; padding:6px 16px; border-radius:99px; margin-bottom:20px; }
        .hero h1 { font-family:'Anton',sans-serif; font-size:48px; color:white; letter-spacing:2px; line-height:1.1; }
        .hero h1 span { color:#FFBB00; }
        .hero p { color:rgba(255,255,255,0.55); font-size:15px; margin-top:12px; max-width:500px; margin-left:auto; margin-right:auto; }

        /* Filter bar */
        .filter-bar { max-width:1200px; margin:0 auto; padding:28px 32px 0; display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
        .filter-chip {
            padding:7px 16px; border-radius:99px; font-size:12px; font-weight:600;
            text-decoration:none; border:1.5px solid #e2e8f0; color:#64748b;
            background:white; transition:all 0.2s; cursor:pointer;
        }
        .filter-chip:hover, .filter-chip.active { background:#1A3365; color:white; border-color:#1A3365; }
        .search-form { margin-left:auto; display:flex; gap:6px; }
        .search-input {
            padding:8px 14px; border:1.5px solid #e2e8f0; border-radius:10px;
            font-size:13px; background:white; width:220px;
        }
        .search-btn { padding:8px 14px; background:#FFBB00; color:#1A3365; border:none; border-radius:10px; font-weight:700; cursor:pointer; font-size:13px; }

        /* Grid */
        .articles-grid { max-width:1200px; margin:0 auto; padding:28px 32px 60px; display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
        @media(max-width:900px){ .articles-grid{ grid-template-columns:repeat(2,1fr); } }
        @media(max-width:600px){ .articles-grid{ grid-template-columns:1fr; } }

        /* Card */
        .article-card { background:white; border-radius:20px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.06); transition:all 0.3s; text-decoration:none; display:block; color:inherit; }
        .article-card:hover { transform:translateY(-6px); box-shadow:0 12px 32px rgba(26,51,101,0.12); }
        .card-cover { position:relative; height:190px; overflow:hidden; }
        .card-cover img { width:100%; height:100%; object-fit:cover; transition:transform 0.5s; }
        .article-card:hover .card-cover img { transform:scale(1.05); }
        .card-cover-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(10,18,40,0.5) 0%, transparent 60%); }
        .card-cat { position:absolute; top:12px; left:12px; padding:4px 10px; border-radius:99px; font-size:10px; font-weight:700; }
        .cat-berita { background:#1A3365; color:white; }
        .cat-edukasi { background:#059669; color:white; }
        .cat-kebijakan { background:#7c3aed; color:white; }
        .cat-pengumuman { background:#d97706; color:white; }
        .card-body { padding:20px; }
        .card-title { font-size:15px; font-weight:700; color:#1e293b; line-height:1.4; margin-bottom:8px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .card-excerpt { font-size:12px; color:#94a3b8; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:14px; }
        .card-meta { display:flex; align-items:center; gap:6px; font-size:11px; color:#94a3b8; }
        .card-meta .dot { width:3px; height:3px; background:#cbd5e1; border-radius:50%; }

        /* Pagination */
        .pagination-wrap { max-width:1200px; margin:0 auto; padding:0 32px 60px; display:flex; justify-content:center; }
        .pagination-wrap nav { display:flex; gap:6px; }

        /* Empty */
        .empty { text-align:center; padding:80px 20px; color:#94a3b8; grid-column:1/-1; }
        .empty i { font-size:48px; margin-bottom:16px; display:block; }

        footer { background:#1A3365; color:rgba(255,255,255,0.4); text-align:center; padding:24px; font-size:12px; }
        footer a { color:#FFBB00; text-decoration:none; }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('login') }}" class="nav-logo">
        <img src="{{ asset('images/logo-tc.png') }}" alt="Tax Center">
        <span class="nav-brand">TAX CENTER</span>
    </a>
    <div class="nav-links">
        <a href="{{ route('login') }}">Beranda</a>
        <a href="{{ route('artikel.index') }}" style="color:white;">Artikel</a>
        <a href="{{ route('login') }}" class="btn-gold">Masuk LMS</a>
    </div>
</nav>

<!-- Hero -->
<div class="hero">
    <div class="label">Pusat Edukasi Perpajakan</div>
    <h1>ARTIKEL &<br><span>BERITA</span></h1>
    <p>Informasi terkini seputar perpajakan, kebijakan fiskal, dan kegiatan Tax Center UIN SGD Bandung.</p>
</div>

<!-- Filter -->
<div class="filter-bar">
    <a href="{{ route('artikel.index') }}" class="filter-chip {{ !$category ? 'active' : '' }}">Semua</a>
    @foreach($categories as $cat)
        <a href="{{ route('artikel.index', ['kategori' => $cat]) }}"
           class="filter-chip {{ $category == $cat ? 'active' : '' }}">{{ $cat }}</a>
    @endforeach
    <form class="search-form" action="{{ route('artikel.index') }}" method="GET">
        @if($category) <input type="hidden" name="kategori" value="{{ $category }}"> @endif
        <input type="text" name="cari" class="search-input" placeholder="Cari artikel..." value="{{ $search ?? '' }}">
        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>
</div>

<!-- Grid -->
<div class="articles-grid">
    @forelse($articles as $article)
        <a href="{{ route('artikel.show', $article->slug) }}" class="article-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
            <div class="card-cover">
                <img src="{{ $article->cover_url }}" alt="{{ $article->title }}">
                <div class="card-cover-overlay"></div>
                @php
                    $catClass = match($article->category) {
                        'Berita' => 'cat-berita',
                        'Edukasi' => 'cat-edukasi',
                        'Kebijakan' => 'cat-kebijakan',
                        'Pengumuman' => 'cat-pengumuman',
                        default => 'cat-berita'
                    };
                @endphp
                <span class="card-cat {{ $catClass }}">{{ $article->category }}</span>
            </div>
            <div class="card-body">
                <h3 class="card-title">{{ $article->title }}</h3>
                @if($article->excerpt)
                    <p class="card-excerpt">{{ $article->excerpt }}</p>
                @endif
                <div class="card-meta">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ $article->author_name }}</span>
                    <div class="dot"></div>
                    <i class="far fa-calendar"></i>
                    <span>{{ $article->published_at->format('d M Y') }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="empty">
            <i class="fas fa-newspaper"></i>
            <p style="font-weight:600; font-size:16px; color:#475569;">Belum ada artikel</p>
            <p style="font-size:13px; margin-top:6px;">Artikel akan segera hadir. Pantau terus!</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($articles->hasPages())
<div class="pagination-wrap">{{ $articles->links() }}</div>
@endif

<footer>
    <p>© {{ date('Y') }} Tax Center FISIP UIN Sunan Gunung Djati Bandung &nbsp;|&nbsp; <a href="{{ route('login') }}">← Kembali ke Beranda</a></p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ once: true, duration: 700 });</script>
</body>
</html>
