<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TPST App - Sistem Informasi Tempat Pengolahan Sampah Terpadu">
    <title><?php echo e(config('app.name', 'TPST App')); ?> — Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-primary: #0a0a1a;
            --bg-secondary: #1a1a2e;
            --bg-card: rgba(255, 255, 255, 0.04);
            --bg-card-hover: rgba(255, 255, 255, 0.06);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-focus: rgba(0, 212, 255, 0.5);
            --text-primary: #f0f0f5;
            --text-secondary: #8a8a9a;
            --text-muted: #5a5a6e;
            --accent-primary: #00d4ff;
            --accent-secondary: #7c3aed;
            --accent-gradient: linear-gradient(135deg, #00d4ff, #7c3aed);
            --error-color: #ff4757;
            --success-color: #2ed573;
            --input-bg: rgba(255, 255, 255, 0.05);
            --shadow-glow: 0 0 40px rgba(0, 212, 255, 0.15);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: stretch;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(0, 212, 255, 0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(124, 58, 237, 0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 40% 80%, rgba(0, 212, 255, 0.05) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite;
        }

        @keyframes bgShift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(2%, -2%) rotate(1deg); }
            66% { transform: translate(-1%, 1%) rotate(-0.5deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--accent-primary);
            border-radius: 50%;
            opacity: 0;
            animation: floatUp linear infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-duration: 12s; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-duration: 15s; animation-delay: 2s; }
        .particle:nth-child(3) { left: 35%; animation-duration: 10s; animation-delay: 4s; }
        .particle:nth-child(4) { left: 50%; animation-duration: 14s; animation-delay: 1s; }
        .particle:nth-child(5) { left: 65%; animation-duration: 11s; animation-delay: 3s; }
        .particle:nth-child(6) { left: 75%; animation-duration: 13s; animation-delay: 5s; }
        .particle:nth-child(7) { left: 85%; animation-duration: 16s; animation-delay: 2.5s; }
        .particle:nth-child(8) { left: 92%; animation-duration: 9s; animation-delay: 6s; }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            90% {
                opacity: 0.2;
            }
            100% {
                transform: translateY(-10vh) scale(1);
                opacity: 0;
            }
        }

        /* Grid Lines (decoration) */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Layout */
        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Hero Side */
        .hero-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
        }

        .hero-content {
            max-width: 520px;
            animation: fadeInUp 0.8s ease-out;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 2.5rem;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: var(--accent-gradient);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 212, 255, 0.25);
            position: relative;
        }

        .brand-icon::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            background: var(--accent-gradient);
            z-index: -1;
            opacity: 0.4;
            filter: blur(12px);
        }

        .brand-icon svg {
            width: 28px;
            height: 28px;
            fill: white;
        }

        .brand-text {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1.25rem;
            color: var(--text-primary);
        }

        .hero-title span {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
        }

        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(0, 212, 255, 0.08);
            border: 1px solid rgba(0, 212, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon svg {
            width: 18px;
            height: 18px;
            stroke: var(--accent-primary);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Login Side */
        .login-side {
            width: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .login-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(to bottom, transparent, var(--border-color), transparent);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: var(--shadow-glow),
                        0 32px 64px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            stroke: var(--text-muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s ease;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input:focus {
            border-color: var(--border-focus);
            background: rgba(255, 255, 255, 0.07);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .form-input:focus ~ .input-icon,
        .input-wrapper:focus-within .input-icon {
            stroke: var(--accent-primary);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--accent-primary);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid var(--border-color);
            border-radius: 5px;
            background: var(--input-bg);
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .checkbox-wrapper input[type="checkbox"]:checked {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .checkbox-wrapper input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 0.85rem;
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: var(--accent-primary);
            transition: width 0.3s ease;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        .forgot-link:hover {
            color: #33ddff;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: var(--accent-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 212, 255, 0.35);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert Messages */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeInUp 0.4s ease-out;
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.2);
            color: #ff6b7a;
        }

        .alert-success {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid rgba(46, 213, 115, 0.2);
            color: #5ae08f;
        }

        .alert svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        /* Footer */
        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            .hero-side {
                padding: 2rem 1.5rem 1rem;
                min-height: auto;
            }

            .hero-content {
                max-width: 100%;
                text-align: center;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-features {
                display: none;
            }

            .login-side {
                width: 100%;
                padding: 1rem 1.5rem 2rem;
            }

            .login-side::before {
                display: none;
            }

            .brand {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.6rem;
            }

            .hero-description {
                font-size: 0.95rem;
            }

            .login-card {
                padding: 1.75rem;
                border-radius: 20px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-animation"></div>
    <div class="grid-overlay"></div>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <!-- Hero Side -->
        <div class="hero-side">
            <div class="hero-content">
                <div class="brand">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="brand-text">TPST App</div>
                </div>

                <h1 class="hero-title">
                    Kelola Sistem<br>
                    <span>TPST Anda</span><br>
                    dengan Mudah.
                </h1>

                <p class="hero-description">
                    Platform digital terpadu untuk manajemen Tempat Pengolahan Sampah Terpadu.
                    Pantau operasional, kelola data, dan buat laporan secara efisien.
                </p>

                <div class="hero-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        </div>
                        <span>Manajemen ritase & produksi harian</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                        </div>
                        <span>Laporan keuangan real-time</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <span>Multi-tenant, multi-user</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </div>
                        <span>Dibuat oleh Kurniawan with ❤️ untuk membantu bisnis lebih berkembang.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Side -->
        <div class="login-side">
            <div class="login-card">
                <div class="login-header">
                    <h2>Selamat Datang</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="alert alert-error">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <span><?php echo e($errors->first()); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session('status')): ?>
                    <div class="alert alert-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span><?php echo e(session('status')); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login.submit')); ?>" id="loginForm">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label class="form-label" for="login">Email / Username</label>
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="login"
                                name="login"
                                class="form-input"
                                placeholder="Masukkan email atau username"
                                value="<?php echo e(old('login')); ?>"
                                required
                                autofocus
                            >
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Masukkan password"
                                required
                            >
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                                <svg id="eyeIcon" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkbox-label">Ingat Saya</span>
                        </label>
                        <a href="<?php echo e(route('password.request')); ?>" class="forgot-link">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        Masuk
                    </button>
                </form>



                <div class="login-footer">
                    &copy; <?php echo e(date('Y')); ?> <a href="https://simpleakunting.biz.id/SolusiConsult.html">SimpleAkunting by Kurniawan with ❤️</a>. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        // Add subtle interaction to the submit button
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.textContent = 'Memproses...';
            submitBtn.style.opacity = '0.7';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/welcome.blade.php ENDPATH**/ ?>