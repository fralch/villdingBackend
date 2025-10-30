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
            --primary-color: #2D3748;
            --secondary-color: #4A5568;
            --accent-color: #FF6B35;
            --accent-hover: #E55A2B;
            --success-color: #48BB78;
            --danger-color: #F56565;
            --warning-color: #ED8936;
            --info-color: #4299E1;
            --light-bg: #F7FAFC;
            --card-bg: #FFFFFF;
            --text-primary: #2D3748;
            --text-secondary: #718096;
            --border-color: #E2E8F0;
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light-bg);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .main-container {
            background-color: var(--light-bg);
            min-height: 100vh;
            border-radius: 0;
        }

        @media (min-width: 768px) {
            .main-container {
                margin: 20px;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-lg);
            }
        }

        .navbar {
            background: var(--card-bg);
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            color: white;
            padding: 8px;
            border-radius: var(--border-radius-sm);
            margin-right: 12px;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: var(--border-radius-sm);
            transition: all 0.3s ease;
            margin: 0 4px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-color) !important;
            background-color: rgba(255, 107, 53, 0.1);
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            background: var(--card-bg);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-hover), #D44A1C);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
        }

        .btn-secondary {
            background-color: var(--text-secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-warning {
            border: 2px solid var(--warning-color);
            color: var(--warning-color);
            background: transparent;
        }

        .btn-outline-warning:hover {
            background: var(--warning-color);
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            border: 2px solid var(--danger-color);
            color: var(--danger-color);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--danger-color);
            color: white;
            transform: translateY(-1px);
        }

        .table {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .table tbody td {
            padding: 1rem;
            border-color: var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.05);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, var(--info-color), #3182CE) !important;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, var(--danger-color), #E53E3E) !important;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, var(--text-secondary), var(--primary-color)) !important;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, var(--success-color), #38A169) !important;
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.1), rgba(56, 161, 105, 0.1));
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.1), rgba(229, 62, 62, 0.1));
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(66, 153, 225, 0.1), rgba(49, 130, 206, 0.1));
            color: var(--info-color);
            border-left: 4px solid var(--info-color);
        }

        .profile-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-img-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .is-invalid {
            border-color: var(--danger-color);
        }

        .is-invalid:focus {
            border-color: var(--danger-color);
            box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            body {
                background: var(--light-bg);
            }

            .main-container {
                margin: 0;
                border-radius: 0;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card {
                margin: 0.5rem 0;
                border-radius: var(--border-radius-sm);
            }

            .card-header {
                padding: 1rem;
                font-size: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .table-responsive {
                border-radius: var(--border-radius);
                margin: -1px;
            }

            .table {
                font-size: 0.875rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }

            .btn {
                padding: 0.65rem 1rem;
                font-size: 0.95rem;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
                margin-bottom: 0;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }

            .navbar-brand i {
                padding: 6px;
                margin-right: 8px;
            }

            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
            }

            .alert {
                font-size: 0.9rem;
                padding: 0.875rem 1rem;
            }

            .badge {
                padding: 4px 10px;
                font-size: 0.7rem;
            }

            .profile-img {
                width: 40px;
                height: 40px;
            }

            h1, .h1 {
                font-size: 1.75rem;
            }

            h2, .h2 {
                font-size: 1.5rem;
            }

            h3, .h3 {
                font-size: 1.25rem;
            }

            h4, .h4 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                padding: 0;
            }

            .container {
                padding: 0 0.75rem;
            }

            .card {
                border-radius: 0;
                margin: 0;
            }

            .card-body {
                padding: 0.875rem;
            }

            .card-header {
                padding: 0.875rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.5rem 0.35rem;
                font-size: 0.8rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
            }

            .navbar {
                padding: 0.75rem 0;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand i {
                font-size: 1rem;
                padding: 4px;
            }

            .badge {
                padding: 3px 8px;
                font-size: 0.65rem;
            }

            .profile-img {
                width: 36px;
                height: 36px;
            }

            .form-control,
            .form-select {
                font-size: 1rem;
            }

            h1, .h1 {
                font-size: 1.5rem;
            }

            h2, .h2 {
                font-size: 1.35rem;
            }

            h3, .h3 {
                font-size: 1.15rem;
            }

            h4, .h4 {
                font-size: 1rem;
            }
        }

        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shadow-custom {
            box-shadow: var(--shadow-lg);
        }

        .border-accent {
            border-color: var(--accent-color) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .bg-gradient-accent {
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-hover);
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
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
