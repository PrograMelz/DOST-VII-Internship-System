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
        $headingTextColor = optional($systemSettings)->heading_text_color ?? '#333333';
        $bodyTextColor = optional($systemSettings)->body_text_color ?? '#333333';
    @endphp
    @if ($systemSettings && $systemSettings->system_logo)
        <link rel="icon" href="{{ asset($systemSettings->system_logo) }}">
    @endif
    <title>{{ $systemSettings->system_short_name ?? 'Internship System' }}</title>
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
            --heading-text-color: {{ $headingTextColor }};
            --body-text-color: {{ $bodyTextColor }};
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--secondary-color);
            color: var(--body-text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            animation: slideUp 0.5s ease-out;
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

        .logo-section {
            margin-bottom: 50px;
        }

        .logo-section h1 {
            color: var(--heading-text-color);
            font-size: 48px;
            margin-bottom: 15px;
        }

        .logo-section p {
            color: var(--body-text-color);
            font-size: 18px;
            margin-bottom: 30px;
        }

        .login-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .login-card.admin,
        .login-card.intern {
            border-top: 4px solid var(--primary-color);
        }

        .login-card-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }

        .login-card h2 {
            color: var(--heading-text-color);
            margin-bottom: 15px;
            font-size: 24px;
        }

        .login-card p {
            color: var(--body-text-color);
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.6;
        }

        .login-button {
            display: inline-block;
            background: var(--button-color);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
        }

        .features {
            margin-top: 60px;
            color: var(--body-text-color);
        }

        .features h3 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .features-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .feature-item p {
            font-size: 14px;
            margin: 0;
        }

        @media (max-width: 768px) {
            .logo-section h1 {
                font-size: 32px;
            }

            .login-buttons {
                grid-template-columns: 1fr;
            }

            .login-card {
                padding: 30px 20px;
            }
        }

        .web-text {
            color: var(--body-text-color);
        }
    </style>
</head>
<body>
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
    @endphp
    <div class="container">
        <div class="logo-section">
            @if ($systemSettings && $systemSettings->system_logo)
                <div style="margin-bottom: 20px;">
                    <img src="{{ asset($systemSettings->system_logo) }}" alt="System Logo" style="max-height: 100px; width: auto;">
                </div>
            @endif
            <h1>{{ $systemSettings->system_long_name ?? '🎓 Internship Management System' }}</h1>
            <p class="web-text">Complete attendance and management solution for interns</p>
        </div>

        <div class="login-buttons">
            <!-- Admin Login Card -->
            <div class="login-card admin">
                <div class="login-card-icon">🔐</div>
                <h2>Admin Login</h2>
                <p>Access the admin dashboard to manage interns, monitor attendance, and generate reports.</p>
                <a href="{{ route('admin.login') }}" class="login-button">Go to Admin Login</a>
            </div>

            <!-- Intern Login Card -->
            <div class="login-card intern">
                <div class="login-card-icon">👤</div>
                <h2>Intern Login</h2>
                <p>View your attendance records, DTR, and manage your internship profile.</p>
                <a href="{{ route('intern.login') }}" class="login-button">Go to Intern Login</a>
            </div>
        </div>

        <!-- Quick Access to QR Scanner -->
        <div style="margin-top: 40px;">
            <a href="{{ route('qr-attendance') }}" style="color: var(--body-text-color); text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block; padding: 10px 20px; border: 2px solid var(--body-text-color); border-radius: 8px;">
                📱 QR Code Scanner
            </a>
        </div>

        <div class="features">
            <h3>✨ Features</h3>
            <div class="features-list">
                <div class="feature-item">
                    <p>📊 Real-time Attendance Tracking</p>
                </div>
                <div class="feature-item">
                    <p>👥 Intern Management</p>
                </div>
                <div class="feature-item">
                    <p>📋 Comprehensive Reports</p>
                </div>
                <div class="feature-item">
                    <p>🔒 Secure Authentication</p>
                </div>
                <div class="feature-item">
                    <p>📱 QR Code Integration</p>
                </div>
                <div class="feature-item">
                    <p>⏰ DTR Generation</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
