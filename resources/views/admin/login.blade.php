<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Tax Center UIN SGD</title>
    <link rel="icon" href="{{ asset('images/TAXCENTER.png') }}" type="image/webp">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f1f3d 0%, #1A3365 100%);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            text-align: center;
        }
        .logo { width: 64px; margin-bottom: 16px; }
        .brand { font-family: 'Anton', sans-serif; font-size: 24px; color: #1A3365; letter-spacing: 1px; line-height: 1; margin-bottom: 4px; }
        .sub-brand { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 32px; }

        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-group input {
            width: 100%; padding: 12px 16px; border: 1.5px solid #e2e8f0;
            border-radius: 12px; font-size: 14px; font-family: 'Inter', sans-serif;
            transition: all 0.2s; background: #f8fafc;
        }
        .form-group input:focus { outline: none; border-color: #1A3365; background: white; box-shadow: 0 0 0 3px rgba(26,51,101,0.08); }
        
        .btn-login {
            width: 100%; padding: 14px; background: #FFBB00; color: #1A3365;
            font-weight: 800; font-size: 15px; border: none; border-radius: 12px;
            cursor: pointer; transition: all 0.2s; margin-top: 8px;
        }
        .btn-login:hover { background: #FFD000; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255,187,0,0.3); }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 500; text-align: left; display: flex; align-items: flex-start; gap: 8px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; margin-top: 24px; transition: color 0.2s; }
        .back-link:hover { color: #1A3365; }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="{{ asset('images/logo-tc.png') }}" alt="Tax Center Logo" class="logo">
        <div class="brand">TAX CENTER</div>
        <div class="sub-brand">Panel Admin Artikel</div>

        @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle" style="margin-top:2px;"></i> {{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle" style="margin-top:2px;"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error"><i class="fas fa-exclamation-circle" style="margin-top:2px;"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@taxcenter.com" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px; text-align: left;">
                <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;accent-color:#1A3365;cursor:pointer;">
                <label for="remember" style="font-size:13px;font-weight:500;color:#64748b;cursor:pointer;margin-bottom:0;text-transform:none;letter-spacing:0;">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login">
                Masuk Panel <i class="fas fa-sign-in-alt" style="margin-left:4px;"></i>
            </button>
        </form>

        <a href="{{ url('/') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Website Utama
        </a>
    </div>

</body>
</html>
