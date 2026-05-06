<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Expense Tracker') }} - Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Admin Theme -->
    <style>
        :root {
            --admin-primary: #1e293b;
            --admin-secondary: #334155;
            --admin-accent: #3b82f6;
            --admin-success: #22c55e;
            --admin-warning: #f59e0b;
            --admin-danger: #ef4444;
            --admin-light: #f8fafc;
            --admin-dark: #0f172a;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #1a1a1a;
        }

        .admin-navbar {
            background: linear-gradient(135deg, var(--admin-dark) 0%, var(--admin-primary) 50%, var(--admin-secondary) 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .admin-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 16px !important;
        }

        .admin-navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }

        .admin-navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .admin-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }

        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .admin-card .card-header {
            background: var(--admin-light);
            color: var(--admin-primary);
            font-weight: 600;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 20px;
        }

        .btn-admin {
            background: var(--admin-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            background: var(--admin-secondary);
            transform: translateY(-1px);
        }

        .admin-sidebar {
            background: white;
            border-right: 1px solid #e5e7eb;
            min-height: calc(100vh - 76px);
            padding: 20px 0;
        }

        .admin-sidebar .nav-link {
            color: var(--admin-primary);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.2s ease;
        }

        .admin-sidebar .nav-link:hover {
            background: var(--admin-light);
            color: var(--admin-accent);
        }

        .admin-sidebar .nav-link.active {
            background: var(--admin-accent);
            color: white;
        }

        .admin-stats {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            color: white;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .admin-stats h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .admin-stats small {
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table thead th {
            background: var(--admin-primary);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background: var(--admin-light);
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check me-2"></i>
                Admin Panel
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                            <i class="bi bi-people me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.targets*') ? 'active' : '' }}" href="{{ route('admin.targets') }}">
                            <i class="bi bi-bullseye me-1"></i>Targets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                            <i class="bi bi-tags me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}" href="{{ route('admin.activity-logs') }}">
                            <i class="bi bi-activity me-1"></i>Activity Logs
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-badge me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <main class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
