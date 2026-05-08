<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Expense Tracker') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-green: #0d4f2f;
            --dark-green: #042418;
            --medium-green: #1a5f3f;
            --accent-green: #2e8b57;
            --light-green: #d4f1e0;
        }

        body {
            background: linear-gradient(135deg, #f0fdf9 0%, #dcfce7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #000000;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .cursor-glow {
            position: fixed;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.22) 0%, rgba(34, 197, 94, 0) 70%);
            transform: translate(-50%, -50%);
            transition: transform 0.08s linear;
        }

        .hero-section {
            background: linear-gradient(135deg, #021a0f 0%, #042418 50%, #0d4f2f 100%);
            color: white;
            padding: 100px 0 120px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 50px 50px;
            margin-bottom: -50px;
        }

        .hero-section::before {
            content: '$  $  $';
            position: absolute;
            font-size: 6rem;
            letter-spacing: 2rem;
            color: rgba(255, 255, 255, 0.07);
            left: -10%;
            top: 18%;
            animation: moneyFlow 16s linear infinite;
            pointer-events: none;
        }

        .hero-section::after {
            content: '$  $  $';
            position: absolute;
            font-size: 5rem;
            letter-spacing: 1.6rem;
            color: rgba(187, 247, 208, 0.12);
            right: -20%;
            bottom: 10%;
            animation: moneyFlowReverse 14s linear infinite;
            pointer-events: none;
        }

        .hero-section h1 {
            font-weight: bold;
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero-section .lead {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,249,0.98) 100%);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(13, 79, 47, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(13, 79, 47, 0.14);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(4px);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--accent-green), var(--medium-green));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(13, 79, 47, 0.15);
            border-color: var(--primary-green);
        }

        .feature-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 50%, var(--accent-green) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.3);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(13, 79, 47, 0.4);
        }

        .feature-card h4 {
            font-weight: 700;
            color: #000000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #000000;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 50%, var(--accent-green) 100%);
            border: none;
            border-radius: 16px;
            font-weight: 700;
            padding: 16px 40px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(13, 79, 47, 0.3);
            position: relative;
            overflow: hidden;
            font-size: 1.1rem;
            color: #ffffff !important;
        }

        .hero-btn {
            min-width: 180px;
            border-width: 2px !important;
            letter-spacing: 0.2px;
            position: relative;
            z-index: 2;
        }

        .hero-btn.btn-primary {
            border: 2px solid rgba(255, 255, 255, 0.7) !important;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--medium-green) 0%, var(--primary-green) 50%, var(--dark-green) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.4);
            color: #ffffff !important;
        }

        .hero-btn.btn-primary:hover,
        .cta-card .btn-primary.hero-btn:hover {
            color: #ffffff !important;
        }

        .btn-outline-secondary {
            border: 3px solid var(--primary-green);
            color: var(--primary-green) !important;
            border-radius: 16px;
            font-weight: 700;
            padding: 16px 40px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            background: white !important;
        }

        .hero-btn.btn-outline-secondary {
            border-color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            backdrop-filter: blur(4px);
        }

        .btn-outline-secondary:hover {
            background: var(--primary-green) !important;
            border-color: var(--primary-green);
            color: white !important;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.3);
        }

        .hero-btn.btn-outline-secondary:hover {
            background: #ffffff !important;
            color: var(--primary-green) !important;
            border-color: #ffffff !important;
        }

        .cta-card .btn-outline-secondary.hero-btn {
            color: var(--primary-green) !important;
            background: #ffffff !important;
            border-color: var(--primary-green) !important;
        }

        .cta-card .btn-outline-secondary.hero-btn:hover {
            background: var(--primary-green) !important;
            color: #ffffff !important;
            border-color: var(--primary-green) !important;
        }

        .cta-card {
            background: white;
            border-radius: 28px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(13, 79, 47, 0.1);
            border: 3px solid var(--light-green);
            position: relative;
            overflow: hidden;
        }

        .cta-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(13, 79, 47, 0.03) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        .cta-card h3 {
            font-weight: 800;
            color: #000000;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }

        @keyframes moneyFlow {
            from { transform: translateX(0); }
            to { transform: translateX(140%); }
        }

        @keyframes moneyFlowReverse {
            from { transform: translateX(0); }
            to { transform: translateX(-150%); }
        }
        
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-10px); }
            70% { transform: translateY(-5px); }
            90% { transform: translateY(-2px); }
        }
        
        @keyframes wiggle {
            0%, 7% { transform: rotate(0deg); }
            25% { transform: rotate(3deg); }
            50% { transform: rotate(-3deg); }
            75% { transform: rotate(1deg); }
            100% { transform: rotate(0deg); }
        }
        
        .fun-hover:hover {
            animation: bounce 1s ease-in-out;
        }
        
        .icon-fun:hover {
            animation: wiggle 0.5s ease-in-out;
        }
        
        /* Extra fun interactions */
        .hero-section h1:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover h4 {
            color: #0d4f2f;
            transition: color 0.3s ease;
        }
        
        .cta-card:hover {
            transform: translateY(-5px) scale(1.02);
            transition: all 0.3s ease;
        }
        
        /* Button press effect */
        .btn:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
        
        /* Floating animation for hero buttons */
        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .animate-fade-in:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .animate-fade-in:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* New Simple Button Styles */
        .register-btn, .login-btn {
            display: inline-block;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }
        
        .register-btn {
            background: #0d4f2f;
            color: white;
        }
        
        .register-btn:hover {
            background: #1a5f3f;
            transform: translateY(-2px);
        }
        
        .login-btn {
            background: white;
            color: #0d4f2f;
            border-color: #0d4f2f;
        }
        
        .login-btn:hover {
            background: #0d4f2f;
            color: white;
            transform: translateY(-2px);
        }

        .btn {
            cursor: pointer !important;
            position: relative;
            z-index: 10;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0;
                border-radius: 0 0 40px 40px;
            }
            
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section .lead {
                font-size: 1.1rem;
            }
            
            .feature-card {
                padding: 30px 20px;
                margin-bottom: 2rem;
            }
            
            .feature-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div id="cursorGlow" class="cursor-glow"></div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4 animate-fade-in">
                        <i class="bi bi-wallet2 me-3"></i>
                        Smart Expense Tracker
                    </h1>
                    <p class="lead mb-4 animate-fade-in">
                        Take control of your finances with our powerful expense tracking application.
                        Monitor your spending, categorize expenses, and gain insights into your financial habits.
                    </p>
                    <div class="d-flex justify-content-center gap-3 animate-fade-in">
                        <a href="{{ route('register') }}" class="btn btn-primary hero-btn me-2">
                            Get Started
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary hero-btn">
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-4" style="color: #000000;">Powerful Features</h2>
                    <p class="lead" style="color: #000000;">Everything you need to manage your expenses effectively</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Mobile Money Integration</h4>
                        <p>Simply paste your mobile money SMS messages and we'll automatically detect amounts, balances, and suggest categories.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-tags"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Smart Categories</h4>
                        <p>Choose from predefined categories or let our system automatically categorize your expenses based on merchant names and transaction details.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Expense Analytics</h4>
                        <p>Get detailed insights with comprehensive reports, spending analytics, and visual breakdowns of your expense patterns.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Secure & Private</h4>
                        <p>Your financial data is securely stored and accessible only to you. We prioritize your privacy and data security.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Easy Entry</h4>
                        <p>Two ways to add expenses: manual entry with all details or paste mobile money messages for instant parsing and categorization.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100 fun-hover">
                        <div class="feature-icon icon-fun">
                            <i class="bi bi-cloud-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Cloud Sync</h4>
                        <p>Access your expenses from anywhere with automatic cloud synchronization and secure data backup.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="cta-card text-center">
                        <h3 class="fw-bold mb-3" style="color: #000000;">Ready to Take Control?</h3>
                        <p class="mb-4" style="color: #000000;">
                            Join thousands of users who are already managing their expenses effectively with our smart tracking system.
                        </p>
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <a href="{{ route('register') }}" class="btn btn-primary hero-btn btn-lg me-md-2">
                                <i class="bi bi-rocket me-2"></i>Start Tracking Now
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary hero-btn btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Continue
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">
                        <i class="bi bi-copyright me-1"></i>
                        {{ date('Y') }} Expense Tracker.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const glow = document.getElementById('cursorGlow');
        document.addEventListener('mousemove', function(event) {
            glow.style.left = event.clientX + 'px';
            glow.style.top = event.clientY + 'px';
        });
    </script>
</body>
</html>