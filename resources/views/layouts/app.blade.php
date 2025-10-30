<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestión de Usuarios')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: #0d6efd;
            --brand-dark: #0b5ed7;
            --surface: #ffffff;
            --surface-muted: #f8f9fa;
            --text-main: #212529;
            --text-muted: #6c757d;
            --border: #dee2e6;
            --success: #198754;
            --danger: #dc3545;
            --info: #0dcaf0;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--surface-muted);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
        }

        .navbar {
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--text-main) !important;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            color: var(--brand);
            margin-right: 10px;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 8px 12px !important;
            border-radius: var(--radius-sm);
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--brand) !important;
            background-color: rgba(13, 110, 253, 0.08);
        }

        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-body { padding: 1.25rem; }

        .btn { border-radius: var(--radius-sm); font-weight: 500; }
        .btn-primary { background-color: var(--brand); border-color: var(--brand); }
        .btn-primary:hover { background-color: var(--brand-dark); border-color: var(--brand-dark); }
        .btn-outline-primary { border-color: var(--brand); color: var(--brand); }
        .btn-outline-primary:hover { background-color: var(--brand); color: #fff; }
        .btn-outline-danger:hover { color: #fff; }

        .table { border-radius: var(--radius); overflow: hidden; }
        .table thead { background-color: var(--surface-muted); }
        .table thead th {
            border: none;
            padding: 0.875rem 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.03em;
            color: var(--text-muted);
        }
        .table tbody td { vertical-align: middle; }

        .badge { border-radius: 999px; font-weight: 600; }

        .alert { border-radius: var(--radius); }

        .profile-img {
            width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
        }
        .profile-img-large {
            width: 120px; height: 120px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--surface); box-shadow: var(--shadow-md);
        }

        /* Form */
        .form-control, .form-select { border-radius: var(--radius-sm); }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
        .invalid-feedback { font-size: 0.875rem; }

        /* Simple stat card helpers */
        .stat-card .stat-label { text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted); font-size: 0.75rem; }
        .stat-card .stat-value { font-weight: 700; }
        .icon-circle { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background-color: var(--surface-muted); color: var(--brand); }
        .icon-circle.success { color: var(--success); background-color: rgba(25, 135, 84, 0.12); }
        .icon-circle.danger { color: var(--danger); background-color: rgba(220, 53, 69, 0.12); }
        .icon-circle.secondary { color: var(--text-muted); background-color: rgba(108, 117, 125, 0.12); }

        /* Responsive */
        @media (max-width: 768px) {
            .card { border-radius: var(--radius-sm); }
            .navbar-brand { font-size: 1.125rem; }
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-project-diagram me-2"></i>
                Sistema de Gestión
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <i class="fas fa-users me-1"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.create') }}">
                            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Por favor corrige los siguientes errores:</strong>
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

    <!-- Footer -->
    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Sistema de Gestión de Proyectos. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
