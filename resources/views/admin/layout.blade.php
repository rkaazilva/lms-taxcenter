<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — Tax Center UIN SGD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #F4F6FA; color: #1e293b; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 240px; background: #1A3365;
            display: flex; flex-direction: column;
            z-index: 50; box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }
        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo .brand { color: #FFBB00; font-family:'Anton',sans-serif; font-size: 18px; letter-spacing: 1px; }
        .sidebar-logo .sub { color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label { color: rgba(255,255,255,0.25); font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; padding: 0 8px; margin: 16px 0 6px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,0.6); font-size: 13px; font-weight: 500;
            text-decoration: none; transition: all 0.2s; margin-bottom: 2px;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,187,0,0.12);
            color: #FFBB00;
        }
        .nav-item i { width: 16px; text-align: center; font-size: 12px; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.08); }

        /* Main */
        .main { margin-left: 240px; min-height: 100vh; }
        .topbar {
            background: white; padding: 0 32px;
            height: 64px; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 30;
        }
        .topbar-title { font-size: 15px; font-weight: 700; color: #1A3365; }
        .topbar-user { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 36px; height: 36px; background: #FFBB00; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1A3365; font-weight: 800; font-size: 14px; }
        .content { padding: 32px; }

        /* Alert */
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">TAX CENTER</div>
            <div class="sub">Admin Panel</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Menu</div>
            <a href="{{ route('admin.articles.index') }}" class="nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Kelola Artikel
            </a>
            <a href="{{ route('artikel.index') }}" target="_blank" class="nav-item">
                <i class="fas fa-globe"></i> Lihat Website
            </a>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item" style="color:rgba(255,100,100,0.7); background:none; border:none; width:100%; text-align:left; cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="main">
        <div class="topbar">
            <span class="topbar-title">@yield('page-title', 'Admin Panel')</span>
            <div class="topbar-user">
                <span style="font-size:12px; color:#64748b;">{{ Auth::user()->name ?? 'Admin' }}</span>
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            </div>
        </div>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</body>
</html>
