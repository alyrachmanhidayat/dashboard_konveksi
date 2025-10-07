<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Change title to dashboard-konveksi -->
    <title>dashboard-konveksi</title>

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bss-overrides.css') }}">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #3f51b5 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        .login-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }

        .form-check-label {
            color: #5a5c69;
            font-weight: 500;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #4e73df 0%, #3f51b5 100%);
            color: white;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #3f51b5 0%, #4e73df 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
        }

        .forgot-password {
            display: block;
            text-align: center;
            color: #4e73df;
            text-decoration: none;
            font-weight: 500;
            margin-top: 15px;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #3f51b5;
            text-decoration: underline;
        }

        .error-message {
            color: #e74a3b;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4e73df 0%, #3f51b5 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="company-logo">DK</div>
            <h2>Selamat Datang Kembali</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <div class="login-body">
            <!-- Session Status -->
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="masukkan email Anda">
                    @error('email')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input id="password"
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="masukkan kata sandi">
                    @error('password')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="checkbox-group">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Ingat Saya
                    </label>
                </div>

                <button type="submit" class="login-btn">
                    Masuk ke Akun
                </button>
            </form>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add some animation effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation when page loads
            const container = document.querySelector('.login-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';

            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>

</html>