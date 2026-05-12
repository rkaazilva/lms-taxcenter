<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} — Tax Center UIN SGD</title>
    <meta name="description" content="{{ $article->excerpt ?: Str::limit(strip_tags($article->body), 150) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anton&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter', sans-serif; background:#F4F6FA; color:#1e293b; }

        nav { background:#1A3365; padding:0 32px; height:64px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:50; box-shadow:0 2px 20px rgba(0,0,0,0.2); }
        .nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-logo img { height:32px; }
        .nav-brand { color:white; font-family:'Anton',sans-serif; font-size:16px; letter-spacing:1px; }
        .nav-links { display:flex; align-items:center; gap:8px; }
        .nav-links a { color:rgba(255,255,255,0.7); text-decoration:none; font-size:13px; font-weight:500; padding:6px 12px; border-radius:8px; transition:all 0.2s; }
        .nav-links a:hover { color:white; }
        .nav-links a.btn-gold { background:#FFBB00; color:#1A3365; font-weight:700; border-radius:10px; }

        /* Cover hero */
        .article-hero {
            position:relative; height:420px; overflow:hidden;
            background:#1A3365;
        }
        .article-hero img { width:100%; height:100%; object-fit:cover; opacity:0.5; }
        .article-hero-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(10,18,40,0.9) 0%, rgba(10,18,40,0.3) 100%); }
        .article-hero-content { position:absolute; bottom:0; left:0; right:0; padding:40px 32px; max-width:900px; margin:0 auto; }
        @media(max-width:900px){ .article-hero-content{ max-width:100%; } }

        .article-cat { display:inline-block; padding:4px 12px; border-radius:99px; font-size:11px; font-weight:700; margin-bottom:14px; }
        .cat-berita { background:#1A3365; color:white; border:1px solid rgba(255,255,255,0.3); }
        .cat-edukasi { background:#059669; color:white; }
        .cat-kebijakan { background:#7c3aed; color:white; }
        .cat-pengumuman { background:#d97706; color:white; }

        .article-hero h1 { font-family:'Anton',sans-serif; font-size:36px; color:white; line-height:1.2; letter-spacing:1px; margin-bottom:14px; }
        .article-meta { display:flex; align-items:center; gap:16px; color:rgba(255,255,255,0.5); font-size:12px; flex-wrap:wrap; }
        .article-meta span { display:flex; align-items:center; gap:6px; }

        /* Layout */
        .article-layout { max-width:1100px; margin:0 auto; padding:48px 32px 80px; display:grid; grid-template-columns:1fr 300px; gap:40px; align-items:start; }
        @media(max-width:860px){ .article-layout{ grid-template-columns:1fr; } .sidebar{ display:none; } }

        /* Content */
        .article-body {
            background:white; border-radius:20px; padding:40px;
            box-shadow:0 2px 12px rgba(0,0,0,0.06); line-height:1.9; font-size:15px; color:#374151;
        }
        .article-body h2, .article-body h3 { font-family:'Anton',sans-serif; color:#1A3365; margin:28px 0 12px; letter-spacing:0.5px; }
        .article-body p { margin-bottom:16px; }
        .article-body ul, .article-body ol { margin:12px 0 16px 20px; }
        .article-body li { margin-bottom:6px; }
        .article-body b, .article-body strong { color:#1A3365; }
        .article-body a { color:#1A3365; text-decoration:underline; }
        .article-body blockquote { border-left:4px solid #FFBB00; padding:12px 20px; background:#fefce8; border-radius:0 10px 10px 0; margin:20px 0; font-style:italic; color:#92400e; }

        /* Share */
        .share-bar { margin-top:32px; padding-top:24px; border-top:1px solid #f1f5f9; }
        .share-bar p { font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:10px; }
        .share-btns { display:flex; gap:8px; flex-wrap:wrap; }
        .share-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:600; text-decoration:none; transition:all 0.2s; border:none; cursor:pointer; }
        .share-wa { background:#d1fae5; color:#065f46; }
        .share-wa:hover { background:#a7f3d0; }
        .share-copy { background:#f1f5f9; color:#475569; }
        .share-copy:hover { background:#e2e8f0; }

        /* Sidebar */
        .sidebar {}
        .sidebar-card { background:white; border-radius:16px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,0.06); margin-bottom:20px; }
        .sidebar-title { font-size:12px; font-weight:700; color:#1A3365; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid #FFBB00; display:inline-block; }
        .related-item { display:flex; gap:12px; margin-bottom:14px; padding-bottom:14px; border-bottom:1px solid #f1f5f9; text-decoration:none; color:inherit; }
        .related-item:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
        .related-thumb { width:60px; height:50px; object-fit:cover; border-radius:8px; flex-shrink:0; }
        .related-title { font-size:12px; font-weight:600; color:#1e293b; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .related-date { font-size:10px; color:#94a3b8; margin-top:4px; }
        .related-item:hover .related-title { color:#1A3365; }

        /* Back nav */
        .breadcrumb { max-width:1100px; margin:0 auto; padding:20px 32px 0; display:flex; align-items:center; gap:8px; font-size:12px; color:#94a3b8; }
        .breadcrumb a { color:#1A3365; text-decoration:none; font-weight:500; }
        .breadcrumb a:hover { text-decoration:underline; }

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

<!-- Article Hero -->
<div class="article-hero">
    <img src="{{ $article->cover_url }}" alt="{{ $article->title }}">
    <div class="article-hero-overlay"></div>
    <div style="position:absolute; inset:0; max-width:900px; margin:0 auto; display:flex; align-items:flex-end; padding:40px 32px;">
        <div>
            @php
                $catClass = match($article->category) {
                    'Berita' => 'cat-berita', 'Edukasi' => 'cat-edukasi',
                    'Kebijakan' => 'cat-kebijakan', 'Pengumuman' => 'cat-pengumuman',
                    default => 'cat-berita'
                };
            @endphp
            <span class="article-cat {{ $catClass }}">{{ $article->category }}</span>
            <h1 class="article-hero">{{ $article->title }}</h1>
            <div class="article-meta">
                <span><i class="fas fa-user-circle"></i> {{ $article->author_name }}</span>
                <span><i class="far fa-calendar"></i> {{ $article->published_at->isoFormat('D MMMM Y') }}</span>
                <span><i class="far fa-clock"></i> {{ ceil(str_word_count(strip_tags($article->body)) / 200) }} menit baca</span>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="{{ route('login') }}">Beranda</a>
    <i class="fas fa-chevron-right" style="font-size:9px;"></i>
    <a href="{{ route('artikel.index') }}">Artikel</a>
    <i class="fas fa-chevron-right" style="font-size:9px;"></i>
    <span>{{ Str::limit($article->title, 50) }}</span>
</div>

<!-- Layout -->
<div class="article-layout">
    <!-- Article Body -->
    <div>
        <div class="article-body">
            {!! nl2br($article->body) !!}
        </div>

        <!-- Share -->
        <div class="share-bar" style="background:white; border-radius:20px; padding:24px 40px; margin-top:0; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
            <p>Bagikan Artikel</p>
            <div class="share-btns">
                <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->url()) }}"
                   target="_blank" class="share-btn share-wa">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <button onclick="copyLink()" class="share-btn share-copy" id="copyBtn">
                    <i class="fas fa-link"></i> Salin Link
                </button>
            </div>
        </div>

        <!-- Back nav -->
        <div style="margin-top:20px;">
            <a href="{{ route('artikel.index') }}" style="display:inline-flex; align-items:center; gap:8px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Kembali ke Semua Artikel
            </a>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        @if($related->count())
        <div class="sidebar-card">
            <div class="sidebar-title">Artikel Terkait</div>
            @foreach($related as $rel)
            <a href="{{ route('artikel.show', $rel->slug) }}" class="related-item">
                <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" class="related-thumb">
                <div>
                    <div class="related-title">{{ $rel->title }}</div>
                    <div class="related-date">{{ $rel->published_at->format('d M Y') }}</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <div class="sidebar-card">
            <div class="sidebar-title">Tentang Tax Center</div>
            <p style="font-size:12px; color:#64748b; line-height:1.7;">
                Tax Center FISIP UIN Sunan Gunung Djati Bandung adalah pusat edukasi perpajakan terpadu yang berkomitmen mencetak profesional pajak berkualitas.
            </p>
            <a href="https://wa.me/6281234567890" target="_blank"
               style="display:inline-flex; align-items:center; gap:6px; margin-top:14px; background:#FFBB00; color:#1A3365; font-weight:700; font-size:12px; padding:8px 14px; border-radius:10px; text-decoration:none;">
                <i class="fab fa-whatsapp"></i> Hubungi Kami
            </a>
        </div>
    </aside>
</div>

<footer>
    <p>© {{ date('Y') }} Tax Center FISIP UIN Sunan Gunung Djati Bandung &nbsp;|&nbsp; <a href="{{ route('login') }}">← Kembali ke Beranda</a></p>
</footer>

<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        btn.style.background = '#d1fae5';
        btn.style.color = '#065f46';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-link"></i> Salin Link';
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}
</script>
</body>
</html>
