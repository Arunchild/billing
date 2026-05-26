<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AVP Soft</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #311042 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            overflow: hidden;
            position: relative;
        }

        /* Abstract glowing background circles */
        .circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            opacity: 0.5;
        }
        .circle-1 {
            width: 400px;
            height: 400px;
            background: #4f46e5;
            top: -100px;
            left: -100px;
        }
        .circle-2 {
            width: 350px;
            height: 350px;
            background: #db2777;
            bottom: -50px;
            right: -50px;
        }

        .login-container {
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .brand-logo {
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: -0.05em;
            background: linear-gradient(90deg, #818cf8, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
            display: inline-block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
            color: #f8fafc;
        }

        .form-control::placeholder {
            color: #94a3b8;
            opacity: 0.8;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .text-muted-custom {
            color: #94a3b8;
        }
        
        .alert-custom {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            border-radius: 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <!-- Background light circles for premium aesthetic -->
    <div class="circle circle-1"></div>
    <div class="circle circle-2"></div>

    <div class="login-container animate__animated animate__fadeInUp">
        <div class="glass-card">
            <div class="text-center mb-4">
                <span class="brand-logo">AVP <span style="font-weight: 300;">Soft</span></span>
                <p class="text-muted-custom small mb-0">Billing & Inventory Management System</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-custom py-2 px-3 mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success bg-opacity-10 text-success border-success border-opacity-25 py-2 px-3 mb-4" style="border-radius:12px; font-size: 0.85rem; background: rgba(16, 185, 129, 0.15);">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ url('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username (Email)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0" style="border-color: rgba(255, 255, 255, 0.1); border-radius: 12px 0 0 12px; color: #94a3b8;">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" class="form-control border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Enter email or username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0" style="border-color: rgba(255, 255, 255, 0.1); border-radius: 12px 0 0 12px; color: #94a3b8;">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" style="background-color: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.15);">
                        <label class="form-check-label text-muted-custom small" for="remember" style="user-select: none;">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
