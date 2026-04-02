<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'User Dashboard') - {{ config('app.name', 'Padel Booking') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .navbar-user {
            background: #800020;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-user .navbar-brand,
        .navbar-user .nav-link {
            color: white !important;
        }
        .navbar-user .nav-link:hover {
            color: #f8e9ed !important;
        }
        .sidebar-user {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-user .nav-link {
            color: #4a5568;
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 0;
        }
        .sidebar-user .nav-link:hover {
            background: #f8e9ed;
            color: #800020;
        }
        .sidebar-user .nav-link.active {
            background: #800020;
            color: white;
        }
        .sidebar-user .nav-link i {
            width: 24px;
            margin-right: 10px;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #800020, #660019);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
        }
        .btn-outline-custom {
            border: 2px solid #800020;
            color: #800020;
            background: transparent;
            padding: 8px 20px;
            border-radius: 10px;
        }
        .btn-outline-custom:hover {
            background: #800020;
            color: white;
        }
    </style>
</head>
<body>

<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar-user">
            <div class="p-3">
                <div class="text-center mb-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/2917/2917995.png" alt="Logo" width="60">
                    <h5 class="mt-2 mb-0" style="color: #800020;">Padel Arena</h5>
                    <small class="text-muted">User Area</small>
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('user.mybookings') }}" class="nav-link {{ request()->routeIs('user.mybookings') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> My Bookings
                    </a>
                    <a href="{{ route('lapangans.index') }}" class="nav-link {{ request()->routeIs('lapangans.*') ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Lapangan
                    </a>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-danger" style="background: none; border: none; width: 100%; text-align: left;">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-user px-4 py-3">
                <div class="d-flex align-items-center">
                    <span class="text-white">
                        <i class="bi bi-person-circle me-2"></i> {{ Auth::user()->name }}
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="bi bi-calendar me-1"></i> {{ date('d F Y') }}
                    </span>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
