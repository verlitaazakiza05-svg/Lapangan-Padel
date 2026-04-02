<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Booking Padel') - Sistem Booking Lapangan Padel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f4f2;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #4a2c2a;
        }

        /* Navbar Maroon Soft */
        .navbar {
            background-color: #7b2f3f !important;
        }
        .navbar .nav-link {
            color: #f1e6e6 !important;
        }
        .navbar .nav-link.active,
        .navbar .nav-link:hover {
            color: #ffffff !important;
        }
        .navbar .navbar-brand {
            color: #fce6e6 !important;
            font-weight: 600;
        }
        .dropdown-menu {
            background-color: #a0515e;
            border-radius: 5px;
        }
        .dropdown-item {
            color: #fce6e6 !important;
        }
        .dropdown-item:hover {
            background-color: #7b2f3f !important;
            color: #ffffff !important;
        }

        /* Main Content */
        main {
            padding: 20px;
        }

        /* Cards */
        .card {
            border-radius: 10px;
            border: 1px solid #d6bcbc;
            background-color: #fdf0f0;
            box-shadow: none;
        }

        /* Tables */
        .table thead {
            background-color: #d8a7b1;
            color: #4a2c2a;
        }

        /* Buttons */
        .btn-primary {
            background-color: #7b2f3f;
            border-color: #7b2f3f;
        }
        .btn-primary:hover {
            background-color: #5e1f2b;
            border-color: #5e1f2b;
        }

        /* Alerts */
        .alert-success {
            background-color: #d8f0e6;
            color: #27614e;
            border: 1px solid #a3d4c3;
        }
        .alert-danger {
            background-color: #f9d6d6;
            color: #7b2f3f;
            border: 1px solid #e5a1a1;
        }

        /* Footer */
        footer {
            background-color: #7b2f3f;
            color: #fce6e6;
            padding: 10px 0;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-calendar-check"></i> Booking Padel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('lapangans.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-grid"></i> Lapangan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('lapangans.index') }}">Daftar Lapangan</a></li>
                        <li><a class="dropdown-item" href="{{ route('lapangans.create') }}">Tambah Lapangan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('pelanggans.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('pelanggans.index') }}">Daftar Pelanggan</a></li>
                        <li><a class="dropdown-item" href="{{ route('pelanggans.create') }}">Tambah Pelanggan</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar"></i> Booking
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('bookings.index') }}">Daftar Booking</a></li>
                        <li><a class="dropdown-item" href="{{ route('bookings.create') }}">Booking Baru</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('pembayarans.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-cash"></i> Pembayaran
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('pembayarans.index') }}">Daftar Pembayaran</a></li>
                        <li><a class="dropdown-item" href="{{ route('pembayarans.create') }}">Input Pembayaran</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Right Side Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Footer -->
<footer>
    <div class="text-center">
        © {{ date('Y') }} Sistem Booking Lapangan Padel
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</body>
</html>
