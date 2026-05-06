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
    
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf9 0%, #dcfce7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #000000;
            min-height: 100vh;
        }
        
        .navbar-custom {
            background: #0d4f2f;
            box-shadow: 0 2px 10px rgba(13, 79, 47, 0.3);
        }
        
        .navbar-custom .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        
        .navbar-custom .nav-link {
            color: white !important;
            padding: 8px 16px !important;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .navbar-custom .nav-link:hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background: #0d4f2f;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #1a5f3f;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 79, 47, 0.4);
        }
        
        .btn-outline-secondary:hover {
            background: #0d4f2f;
            border-color: #0d4f2f;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 79, 47, 0.4);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .form-control:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 0.2rem rgba(13, 79, 47, 0.25);
        }
        
        .animate-fade-in {
            animation: slideInUp 0.6s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-control {
            border: 2px solid #e8f5e8;
            border-radius: 8px;
            padding: 12px;
        }
        
        .form-control:focus {
            border-color: #0d4f2f;
            box-shadow: 0 0 0 0.2rem rgba(13, 79, 47, 0.25);
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
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
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="bi bi-house-door me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('expenses.index') }}">
                                <i class="bi bi-receipt me-1"></i>Expenses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('expenses.create') }}">
                                <i class="bi bi-plus-circle me-1"></i>Add Expense
                            </a>
                        </li>
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
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
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
                            <a class="nav-link" href="{{ route('login') }}">
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
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
