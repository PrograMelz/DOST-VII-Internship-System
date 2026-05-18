<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $systemSettings = \App\Models\SystemSetting::query()->first();
        $primaryColor = optional($systemSettings)->primary_color ?? '#2E86C1';
        $secondaryColor = optional($systemSettings)->secondary_color ?? '#1B4F72';
        $buttonColor = optional($systemSettings)->button_color ?? '#3498DB';
    @endphp
    @if ($systemSettings && $systemSettings->system_logo)
        <link rel="icon" href="{{ asset($systemSettings->system_logo) }}">
    @endif
    <title>Forgot password – {{ $systemSettings->system_short_name ?? 'Internship System' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            grid-template-columns: 40% 60%;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.22);
            height: min(88vh, 640px);
            animation: shellIn 0.5s ease-out;
        }

        @keyframes shellIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container {
            padding: 30px 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: auto;
            animation: slideLeftIn 0.45s ease-out 0.08s both;
        }

        @keyframes slideLeftIn {
            from { opacity: 0; transform: translateX(28px); }
            to { opacity: 1; transform: translateX(0); }
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
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .panel-caption p {
            font-size: 14px;
            opacity: 0.92;
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
        }
        .login-header h1 {
            color: var(--primary-color);
            font-size: 24px;
            margin-bottom: 8px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .form-group { margin-bottom: 20px; }
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
        .input-wrap:focus-within .input-icon { color: var(--primary-color); }
        .input-wrap input::placeholder { color: #999; }
        .input-wrap.has-toggle input { padding-right: 44px; }
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
        }
        .password-toggle:hover { color: var(--primary-color); }
        .password-toggle i { font-size: 18px; }
        .error-message { color: #e74c3c; font-size: 13px; margin-top: 5px; display: block; }
        .success-message { color: #27ae60; font-size: 13px; margin-top: 5px; display: block; }
        .btn-primary-custom {
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
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-primary-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        .link-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            margin-top: 20px;
            transition: opacity 0.2s;
        }
        .link-back:hover { opacity: 0.85; }
        #step-reset { display: none; }
        #step-reset.show { display: block; }
        #step-email.hide { display: none; }
        .code-input { letter-spacing: 0.35em; font-size: 18px; text-align: center; }

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
        <div class="login-container">
            <div class="login-header">
                @if ($systemSettings && $systemSettings->system_logo)
                    <img src="{{ asset($systemSettings->system_logo) }}" alt="" class="header-logo">
                @endif
                <h1>Reset password</h1>
                <p>{{ $systemSettings->system_long_name ?? 'Internship System' }}</p>
            </div>

        {{-- Step 1: Enter email, send code --}}
        <div id="step-email">
            <p class="form-group" style="margin-bottom: 20px; color: #555;">Enter the email address linked to your intern account. We’ll send you a 6-digit code to reset your password.</p>
            <div class="form-group">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <input type="email" id="email" name="email" placeholder="your@email.com" required autofocus>
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                <span id="email-error" class="error-message" style="display: none;"></span>
            </div>
            <button type="button" class="btn-primary-custom" id="sendCodeBtn">
                <i class="bi bi-send"></i> Send code
            </button>
        </div>

        {{-- Step 2: Enter code + new password --}}
        <div id="step-reset">
            <p id="reset-email-display" class="form-group" style="margin-bottom: 16px; color: #555; font-size: 14px;"></p>
            <input type="hidden" id="reset-email" name="email">
            <div class="form-group">
                <label for="code">6-digit code</label>
                <div class="input-wrap">
                    <input type="text" id="code" name="code" placeholder="000000" maxlength="6" pattern="[0-9]*" inputmode="numeric" class="code-input" autocomplete="one-time-code">
                    <i class="bi bi-shield-lock input-icon"></i>
                </div>
                <span id="code-error" class="error-message" style="display: none;"></span>
            </div>
            <div class="form-group">
                <label for="password">New password</label>
                <div class="input-wrap has-toggle">
                    <input type="password" id="password" name="password" placeholder="At least 8 characters" required minlength="8">
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <span id="password-error" class="error-message" style="display: none;"></span>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm new password</label>
                <div class="input-wrap has-toggle">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password" required minlength="8">
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="password-toggle" id="togglePasswordConfirm" aria-label="Show password">
                        <i class="bi bi-eye" id="toggleIconConfirm"></i>
                    </button>
                </div>
                <span id="password-confirm-error" class="error-message" style="display: none;"></span>
            </div>
            <button type="button" class="btn-primary-custom" id="resetPasswordBtn">
                <i class="bi bi-key"></i> Reset password
            </button>
        </div>

            <a href="{{ route('intern.login') }}" class="link-back">
                <i class="bi bi-arrow-left"></i> Back to login
            </a>
        </div>

        <div class="visual-panel">
            <img src="{{ asset('image/dost_building.png') }}" alt="DOST Building">
            <div class="visual-overlay"></div>
            <div class="panel-caption">
                <h2>Reset & Continue</h2>
                <p>Securely regain access to your intern account</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const stepEmail = document.getElementById('step-email');
            const stepReset = document.getElementById('step-reset');
            const emailInput = document.getElementById('email');
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const resetEmailInput = document.getElementById('reset-email');
            const resetEmailDisplay = document.getElementById('reset-email-display');
            const codeInput = document.getElementById('code');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const resetPasswordBtn = document.getElementById('resetPasswordBtn');

            function showError(id, msg) {
                const el = document.getElementById(id);
                if (el) { el.textContent = msg; el.style.display = msg ? 'block' : 'none'; }
            }
            function clearErrors() {
                ['email-error', 'code-error', 'password-error', 'password-confirm-error'].forEach(function(id) { showError(id, ''); });
            }

            // Only digits in code
            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });

            sendCodeBtn.addEventListener('click', async function() {
                const email = emailInput.value.trim();
                if (!email) {
                    showError('email-error', 'Please enter your email address.');
                    return;
                }
                clearErrors();
                sendCodeBtn.disabled = true;
                sendCodeBtn.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;"></span> Sending...';
                try {
                    const res = await fetch('{{ route("intern.forgot-password.send-code") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ email: email }),
                    });
                    const data = await res.json().catch(function() { return {}; });
                    if (res.ok && data.sent) {
                        resetEmailInput.value = email;
                        resetEmailDisplay.textContent = 'Code sent to ' + email;
                        stepEmail.classList.add('hide');
                        stepReset.classList.add('show');
                        codeInput.value = '';
                        passwordInput.value = '';
                        passwordConfirmInput.value = '';
                        codeInput.focus();
                    } else {
                        showError('email-error', data.message || data.errors?.email?.[0] || 'Failed to send code.');
                    }
                } catch (e) {
                    showError('email-error', 'Something went wrong. Please try again.');
                }
                sendCodeBtn.disabled = false;
                sendCodeBtn.innerHTML = '<i class="bi bi-send"></i> Send code';
            });

            resetPasswordBtn.addEventListener('click', async function() {
                clearErrors();
                const email = resetEmailInput.value;
                const code = codeInput.value.trim();
                const password = passwordInput.value;
                const passwordConfirm = passwordConfirmInput.value;
                if (!code || code.length !== 6) {
                    showError('code-error', 'Please enter the 6-digit code from your email.');
                    return;
                }
                if (password.length < 8) {
                    showError('password-error', 'Password must be at least 8 characters.');
                    return;
                }
                if (password !== passwordConfirm) {
                    showError('password-confirm-error', 'Passwords do not match.');
                    return;
                }
                resetPasswordBtn.disabled = true;
                resetPasswordBtn.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;"></span> Updating...';
                try {
                    const res = await fetch('{{ route("intern.forgot-password.reset") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            email: email,
                            code: code,
                            password: password,
                            password_confirmation: passwordConfirm,
                        }),
                    });
                    const data = await res.json().catch(function() { return {}; });
                    if (res.ok && (data.success || data.redirect)) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password updated',
                            text: data.message || 'You can now log in with your new password.',
                            showConfirmButton: true
                        }).then(function() {
                            window.location.href = data.redirect || '{{ route("intern.login") }}';
                        });
                        return;
                    }
                    const msg = data.message || data.errors?.code?.[0] || data.errors?.password?.[0] || 'Failed to reset password.';
                    showError('code-error', msg);
                } catch (e) {
                    showError('code-error', 'Something went wrong. Please try again.');
                }
                resetPasswordBtn.disabled = false;
                resetPasswordBtn.innerHTML = '<i class="bi bi-key"></i> Reset password';
            });

            document.getElementById('togglePassword').addEventListener('click', function() {
                var isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                document.getElementById('toggleIcon').classList.toggle('bi-eye', !isPassword);
                document.getElementById('toggleIcon').classList.toggle('bi-eye-slash', isPassword);
            });
            document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
                var isPassword = passwordConfirmInput.type === 'password';
                passwordConfirmInput.type = isPassword ? 'text' : 'password';
                document.getElementById('toggleIconConfirm').classList.toggle('bi-eye', !isPassword);
                document.getElementById('toggleIconConfirm').classList.toggle('bi-eye-slash', isPassword);
            });
        });
    </script>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
