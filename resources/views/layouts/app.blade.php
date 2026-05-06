<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Expense Tracker') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Dark Green Theme -->
    <style>
        :root {
            --primary-green: #0d4f2f;
            --dark-green: #042418;
            --darker-green: #021a0f;
            --light-green: #d4f1e0;
            --medium-green: #1a5f3f;
            --accent-green: #2e8b57;
            --success-green: #22c55e;
            --bg-gradient-start: #f0fdf9;
            --bg-gradient-end: #dcfce7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 50%, #e8f5e8 100%);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #1a1a1a;
            line-height: 1.6;
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--darker-green) 0%, var(--dark-green) 50%, var(--primary-green) 100%) !important;
            box-shadow: 0 4px 20px rgba(13, 79, 47, 0.4);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
        }

        .navbar-custom .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }

        .navbar-custom .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 50%, var(--accent-green) 100%);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(13, 79, 47, 0.3);
            position: relative;
            overflow: hidden;
            pointer-events: auto !important;
            z-index: 10;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--medium-green) 0%, var(--dark-green) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 79, 47, 0.3);
        }

        button {
            pointer-events: auto !important;
            position: relative;
            z-index: 10;
        }

        .card::before,
        .card::after {
            pointer-events: none;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 79, 47, 0.3);
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(13, 79, 47, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(13, 79, 47, 0.05) 0%, rgba(46, 139, 87, 0.05) 100%);
            border-bottom: 1px solid rgba(13, 79, 47, 0.1);
            font-weight: 600;
            color: var(--dark-green);
            padding: 20px 24px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e8f5e8;
            transition: all 0.3s ease;
            padding: 12px 16px;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(13, 79, 47, 0.15);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-green);
            margin-bottom: 8px;
        }

        .alert-success {
            background: linear-gradient(135deg, var(--light-green) 0%, #bbf7d0 100%);
            border: 1px solid var(--success-green);
            color: var(--dark-green);
            border-radius: 16px;
            padding: 16px 20px;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
            border: 1px solid #ef4444;
            border-radius: 16px;
            padding: 16px 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border: 1px solid #0ea5e9;
            border-radius: 16px;
            padding: 16px 20px;
        }

        .table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(13, 79, 47, 0.08);
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 16px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .badge {
            border-radius: 20px;
            font-weight: 500;
            padding: 6px 14px;
            font-size: 0.8rem;
        }

        .expense-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-green);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(13, 79, 47, 0.08);
        }

        .expense-card:hover {
            transform: translateX(8px) translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 79, 47, 0.15);
            border-left-width: 6px;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 50%, var(--accent-green) 100%);
            color: white;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        .stats-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(13, 79, 47, 0.4);
        }

        .mobile-money-input {
            background: linear-gradient(135deg, #f8faf9 0%, #f0fdf9 100%);
            border: 2px dashed var(--primary-green);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .mobile-money-input:hover {
            background: var(--light-green);
            border-style: solid;
            border-color: var(--medium-green);
        }

        .mobile-money-input:focus {
            border-style: solid;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(13, 79, 47, 0.15);
        }

        .category-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .animate-fade-in {
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-30px); }
            70% { transform: translateY(-15px); }
            90% { transform: translateY(-4px); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(13, 79, 47, 0.5); }
            50% { box-shadow: 0 0 20px rgba(13, 79, 47, 0.8), 0 0 30px rgba(13, 79, 47, 0.4); }
        }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .animate-slide-left {
            animation: slideInLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-slide-right {
            animation: slideInRight 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        .animate-slide-down {
            animation: slideDown 0.4s ease-out;
        }

        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }

        .shimmer-effect {
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }

        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(13, 79, 47, 0.2);
        }

        .interactive-card {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .interactive-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .interactive-card:hover::before {
            left: 100%;
        }

        .interactive-card:hover {
            transform: translateY(-5px) rotateX(5deg);
            box-shadow: 0 25px 50px rgba(13, 79, 47, 0.15);
        }

        .stagger-animation > * {
            opacity: 0;
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }
        .stagger-animation > *:nth-child(6) { animation-delay: 0.6s; }

        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }

        .notification-badge {
            position: relative;
            animation: bounce 2s infinite;
        }

        .notification-badge::after {
            content: '';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 12px;
            height: 12px;
            background: #dc3545;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        .progress {
            height: 8px;
            border-radius: 10px;
            background-color: rgba(13, 79, 47, 0.1);
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .nav-tabs .nav-link {
            border-radius: 12px 12px 0 0;
            border: none;
            background: rgba(13, 79, 47, 0.05);
            color: var(--dark-green);
            font-weight: 500;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-green);
            color: white;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(13, 79, 47, 0.1);
        }

        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(13, 79, 47, 0.15);
            border: 1px solid rgba(13, 79, 47, 0.1);
        }

        .dropdown-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 2px 8px;
        }

        .dropdown-item:hover {
            background: var(--light-green);
            color: var(--dark-green);
        }

        .form-text {
            color: var(--medium-green);
            font-size: 0.875rem;
        }

        /* Dashboard specific styles */
        .icon-wrapper {
            position: relative;
            z-index: 1;
        }

        .expense-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .expense-item:hover {
            background: linear-gradient(90deg, rgba(13, 79, 47, 0.02) 0%, transparent 100%);
            border-left-color: var(--primary-green);
            transform: translateX(4px);
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .mobile-money-wrapper {
            position: relative;
        }

        .mobile-money-wrapper .mobile-money-icon {
            position: absolute;
            top: 12px;
            right: 12px;
            color: var(--primary-green);
            font-size: 1.2rem;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .mobile-money-wrapper:hover .mobile-money-icon {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-green);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-green);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--medium-green);
        }

        /* Additional enhancements for consistency */
        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 22px;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        /* Enhanced focus states */
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 0.25rem rgba(13, 79, 47, 0.25);
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 79, 47, 0.25), 0 6px 20px rgba(13, 79, 47, 0.3);
        }

        /* Enhanced table styling */
        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, rgba(13, 79, 47, 0.05) 0%, transparent 100%);
            transform: scale(1.01);
        }

        /* Enhanced alert styling */
        .alert {
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-green);
        }

        .alert-success::before {
            background: var(--success-green);
        }

        .alert-danger::before {
            background: #dc3545;
        }

        .alert-info::before {
            background: #0ea5e9;
        }

        /* Enhanced modal styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(13, 79, 47, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }

        .modal-footer {
            border-top: 1px solid rgba(13, 79, 47, 0.1);
        }

        /* Enhanced pagination */
        .pagination .page-link {
            color: var(--primary-green);
            border: 1px solid rgba(13, 79, 47, 0.2);
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-green);
            color: white;
            border-color: var(--primary-green);
        }

        /* Simple navigation link */
        .nav-link-simple {
            color: white !important;
            font-weight: 600;
            padding: 8px 16px !important;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .nav-link-simple:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-wallet2 me-2"></i>
                Expense Tracker
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ Auth::user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">
                                <i class="bi bi-house-door me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('expenses.index') }}">
                                <i class="bi bi-receipt me-1"></i>Expenses
                            </a>
                        </li>
                        @if(!Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('expenses.create') }}">
                                <i class="bi bi-plus-circle me-1"></i>Add Expense
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.index') }}">
                                <i class="bi bi-file-earmark-text me-1"></i>Reports
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ Auth::user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link-simple" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('budgetAlert'))
            <div class="alert alert-info alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-lightning-charge me-2"></i>
                {{ session('budgetAlert') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Custom JS for enhanced interactivity -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add loading state to forms (except login form)
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form:not(#loginForm)');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>