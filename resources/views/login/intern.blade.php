<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
        $primaryColor = optional($systemSettings)->primary_color ?? '#2E86C1';
        $secondaryColor = optional($systemSettings)->secondary_color ?? '#1B4F72';
        $buttonColor = optional($systemSettings)->button_color ?? '#3498DB';
    @endphp
    @if ($systemSettings && $systemSettings->system_logo)
        <link rel="icon" href="{{ asset($systemSettings->system_logo) }}">
    @endif
    <title>{{ $systemSettings->system_short_name ?? 'Internship System' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --button-color: {{ $buttonColor }};
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
            overflow: hidden;
        }

        .page-shell {
            width: 100%;
            max-width: 1080px;
            display: grid;
            grid-template-columns: 60% 40%;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.22);
            animation: slideUp 0.5s ease-out;
            height: min(88vh, 640px);
        }

        .visual-panel {
            position: relative;
            background: #0d1b2a;
        }

        .visual-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(13, 27, 42, 0.25) 0%, rgba(13, 27, 42, 0.65) 100%);
        }

        .panel-caption {
            position: absolute;
            left: 28px;
            right: 28px;
            bottom: 26px;
            color: #fff;
            z-index: 2;
        }

        .panel-caption h2 {
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .panel-caption p {
            font-size: 14px;
            opacity: 0.92;
        }

        .login-container {
            padding: 30px 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .login-header .header-logo {
            max-height: 64px;
            width: auto;
            margin-bottom: 16px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            animation: fadeIn 0.5s ease-out 0.1s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 30px;
            margin-bottom: 8px;
            animation: fadeIn 0.5s ease-out 0.15s both;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
            animation: fadeIn 0.5s ease-out 0.2s both;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            color: #999;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.25s ease;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 15px 12px 46px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .input-wrap input:focus + .input-icon,
        .input-wrap:focus-within .input-icon {
            color: var(--primary-color);
        }

        .input-wrap input::placeholder {
            color: #999;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.25s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-toggle i {
            font-size: 18px;
        }

        .input-wrap.has-toggle input {
            padding-right: 44px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .login-button {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, var(--button-color), var(--primary-color));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fadbd8;
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        .forgot-link {
            color: var(--primary-color);
            font-size: 14px;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 960px) {
            body {
                overflow: auto;
            }

            .page-shell {
                grid-template-columns: 1fr;
                max-width: 520px;
                height: auto;
            }

            .visual-panel {
                min-height: 220px;
            }

            .login-container {
                padding: 28px 24px;
                overflow: visible;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="visual-panel">
            <img src="{{ asset('image/dost_building.png') }}" alt="DOST Building">
            <div class="visual-overlay"></div>
            <div class="panel-caption">
                <h2>Welcome Back</h2>
                <p>Internship Monitoring System</p>
            </div>
        </div>

        <div class="login-container">
            <div class="login-header">
                @if ($systemSettings && $systemSettings->system_logo)
                    <img src="{{ asset($systemSettings->system_logo) }}" alt="" class="header-logo">
                @endif
                <h1>Intern Login</h1>
                <p>{{ $systemSettings->system_long_name ?? 'Internship System' }}</p>
            </div>

            <form method="POST" action="{{ route('intern.login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Enter your username"
                            required
                            autofocus
                        >
                        <i class="bi bi-person input-icon"></i>
                    </div>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap has-toggle">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="login-button">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login
                </button>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ route('intern.forgot-password') }}" class="forgot-link">Forgot password?</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pw = document.getElementById('password');
            var toggle = document.getElementById('togglePassword');
            var icon = document.getElementById('toggleIcon');
            if (pw && toggle && icon) {
                toggle.addEventListener('click', function() {
                    var isPassword = pw.type === 'password';
                    pw.type = isPassword ? 'text' : 'password';
                    icon.classList.toggle('bi-eye', !isPassword);
                    icon.classList.toggle('bi-eye-slash', isPassword);
                    toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                });
            }

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Login failed',
                    text: @json($errors->first('username') ?? $errors->first('password') ?? $errors->first()),
                    showConfirmButton: true
                });
            @endif
        });
    </script>
</body>
</html>
