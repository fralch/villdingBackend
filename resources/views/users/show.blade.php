@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')
<!-- Encabezado sobrio -->
<div class="bg-white rounded-3 p-3 p-md-4 mb-4 shadow-sm border">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="me-3 text-muted"><i class="fas fa-user-circle fa-lg"></i></div>
            <div>
                <h1 class="h4 fw-semibold mb-1 text-dark">Perfil de usuario</h1>
                <p class="mb-0 text-muted">Información detallada del usuario</p>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none"><i class="fas fa-users me-1"></i>Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <!-- User Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-bg"></div>
                    <div class="profile-avatar">
                        @if($user->uri)
                            <img src="{{ $user->uri }}" alt="{{ $user->name }}" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div class="avatar-status {{ $user->is_paid_user ? 'premium' : 'regular' }}"></div>
                    </div>
                </div>

                <div class="profile-body">
                    <div class="profile-info text-center mb-4">
                        <h3 class="profile-name">{{ $user->name }} {{ $user->last_name }}</h3>
                        <p class="profile-email">{{ $user->email }}</p>
                        <div class="profile-code">
                            <span class="code-badge">{{ $user->user_code }}</span>
                        </div>
                    </div>

                    <div class="profile-badges mb-4">
                        @if($user->role === 'admin')
                            <div class="badge-item admin">
                                <i class="fas fa-crown"></i>
                                <span>Administrador</span>
                            </div>
                        @else
                            <div class="badge-item user">
                                <i class="fas fa-user"></i>
                                <span>Usuario</span>
                            </div>
                        @endif

                        @if($user->is_paid_user)
                            <div class="badge-item premium">
                                <i class="fas fa-star"></i>
                                <span>Premium</span>
                            </div>
                        @endif
                    </div>

                    <div class="profile-stats mb-4">
                        <div class="stat-item">
                            <div class="stat-value">{{ $user->projects->count() }}</div>
                            <div class="stat-label">Proyectos</div>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $user->created_at->diffInDays() }}</div>
                            <div class="stat-label">Días activo</div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-action">
                            <i class="fas fa-edit"></i>
                            <span>Editar Usuario</span>
                        </a>
                        <button type="button" class="btn btn-danger btn-action" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i>
                            <span>Eliminar</span>
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-action">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <!-- User Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="row g-4">
                <!-- Personal Information Card -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon personal">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <div class="info-title">
                                <h4>Información Personal</h4>
                                <p>Datos personales del usuario</p>
                            </div>
                        </div>
                        <div class="info-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-signature"></i>
                                            <span>Nombre Completo</span>
                                        </div>
                                        <div class="info-value">{{ $user->name }} {{ $user->last_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Edad</span>
                                        </div>
                                        <div class="info-value">{{ $user->edad ?? 'No especificada' }} años</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-venus-mars"></i>
                                            <span>Género</span>
                                        </div>
                                        <div class="info-value">
                                            @if($user->genero === 'male')
                                                <span class="gender-badge male">
                                                    <i class="fas fa-mars"></i> Masculino
                                                </span>
                                            @elseif($user->genero === 'female')
                                                <span class="gender-badge female">
                                                    <i class="fas fa-venus"></i> Femenino
                                                </span>
                                            @else
                                                <span class="gender-badge other">
                                                    <i class="fas fa-genderless"></i> {{ $user->genero ?? 'No especificado' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-phone"></i>
                                            <span>Teléfono</span>
                                        </div>
                                        <div class="info-value">{{ $user->telefono ?? 'No especificado' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Card -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon contact">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <div class="info-title">
                                <h4>Información de Contacto</h4>
                                <p>Datos de contacto y cuenta</p>
                            </div>
                        </div>
                        <div class="info-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-envelope"></i>
                                            <span>Correo Electrónico</span>
                                        </div>
                                        <div class="info-value">
                                            <a href="mailto:{{ $user->email }}" class="email-link">
                                                {{ $user->email }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-id-card"></i>
                                            <span>Código de Usuario</span>
                                        </div>
                                        <div class="info-value">
                                            <span class="user-code">{{ $user->user_code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information Card -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon account">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-title">
                                <h4>Información de Cuenta</h4>
                                <p>Fechas importantes y actividad</p>
                            </div>
                        </div>
                        <div class="info-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-user-plus"></i>
                                            <span>Fecha de Registro</span>
                                        </div>
                                        <div class="info-value">
                                            <div class="date-info">
                                                <span class="date">{{ $user->created_at->format('d/m/Y') }}</span>
                                                <span class="time">{{ $user->created_at->format('H:i') }}</span>
                                                <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-edit"></i>
                                            <span>Última Actualización</span>
                                        </div>
                                        <div class="info-value">
                                            <div class="date-info">
                                                <span class="date">{{ $user->updated_at->format('d/m/Y') }}</span>
                                                <span class="time">{{ $user->updated_at->format('H:i') }}</span>
                                                <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Card -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon projects">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="info-title">
                                <h4>Proyectos Asociados</h4>
                                <p>Lista de proyectos en los que participa el usuario</p>
                            </div>
                            <div class="info-badge">
                                <span class="projects-count">{{ $user->projects->count() }}</span>
                            </div>
                        </div>
                        <div class="info-body">
                            @if($user->projects->count() > 0)
                                <div class="projects-grid">
                                    @foreach($user->projects as $project)
                                        <div class="project-item">
                                            <div class="project-header">
                                                <div class="project-icon">
                                                    <i class="fas fa-folder-open"></i>
                                                </div>
                                                <div class="project-info">
                                                    <h5 class="project-name">{{ $project->name }}</h5>
                                                    <p class="project-code">{{ $project->code }}</p>
                                                </div>
                                                <div class="project-role">
                                                    <span class="role-badge {{ $project->pivot->is_admin ? 'admin' : 'member' }}">
                                                        @if($project->pivot->is_admin)
                                                            <i class="fas fa-crown"></i>
                                                            Admin
                                                        @else
                                                            <i class="fas fa-user"></i>
                                                            Miembro
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="project-details">
                                                @if($project->location)
                                                    <div class="project-location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span>{{ $project->location }}</span>
                                                    </div>
                                                @endif
                                                @if($project->description)
                                                    <div class="project-description">
                                                        <p>{{ Str::limit($project->description, 100) }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h5>Sin proyectos asignados</h5>
                                    <p>Este usuario no está asociado a ningún proyecto actualmente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    border-radius: 0 0 20px 20px;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--bs-secondary);
}

.header-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.header-subtitle {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.custom-breadcrumb {
    background: transparent;
    border-radius: 10px;
    padding: 0;
    margin: 0;
}

.custom-breadcrumb .breadcrumb-item a {
    color: var(--bs-primary);
    text-decoration: none;
}

.custom-breadcrumb .breadcrumb-item.active {
    color: var(--bs-secondary);
}

/* Profile Card */
.profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
}

.profile-header {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.profile-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--bs-gray-200);
}

.profile-avatar {
    position: absolute;
    bottom: -40px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid white;
    overflow: hidden;
    background: white;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #6c757d;
}

.avatar-status {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
}

.avatar-status.premium {
    background: #ffd700;
}

.avatar-status.regular {
    background: #28a745;
}

.profile-body {
    padding: 3rem 1.5rem 1.5rem;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.profile-email {
    color: #718096;
    margin-bottom: 1rem;
}

.code-badge {
    background: var(--bs-light);
    color: var(--bs-body-color);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid var(--bs-border-color);
    font-weight: 600;
    font-size: 0.9rem;
}

.profile-badges {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.badge-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-item.admin {
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
}

.badge-item.user {
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
}

.badge-item.premium {
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
}

.profile-stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
}

.stat-label {
    font-size: 0.85rem;
    color: #718096;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: #e2e8f0;
}

.profile-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--bs-border-color);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.info-header {
    background: #f8f9fa;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: var(--bs-secondary);
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
}

.info-icon.personal {
    background: var(--bs-light);
}

.info-icon.contact {
    background: var(--bs-light);
}

.info-icon.account {
    background: var(--bs-light);
}

.info-icon.projects {
    background: var(--bs-light);
}

.info-title h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
}

.info-title p {
    margin: 0;
    color: #718096;
    font-size: 0.9rem;
}

.info-badge {
    margin-left: auto;
}

.projects-count {
    background: var(--bs-light);
    color: var(--bs-body-color);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    border: 1px solid var(--bs-border-color);
    font-weight: 700;
    font-size: 1rem;
}

.info-body {
    padding: 1.5rem;
}

.info-item {
    margin-bottom: 1.5rem;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.info-label i {
    color: #718096;
}

.info-value {
    font-size: 1rem;
    color: #2d3748;
    font-weight: 500;
}

.email-link {
    color: var(--bs-primary);
    text-decoration: none;
}

.email-link:hover {
    color: rgba(var(--bs-primary-rgb), .85);
    text-decoration: underline;
}

.user-code {
    background: var(--bs-light);
    color: var(--bs-body-color);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    border: 1px solid var(--bs-border-color);
    font-size: 0.85rem;
    font-weight: 600;
}

.gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.gender-badge.male {
    background: var(--bs-light);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
}

.gender-badge.female {
    background: var(--bs-light);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
}

.gender-badge.other {
    background: var(--bs-light);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
}

.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-info .date {
    font-weight: 600;
}

.date-info .time {
    font-size: 0.9rem;
    color: #718096;
}

/* Projects Grid */
.projects-grid {
    display: grid;
    gap: 1rem;
}

.project-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.project-item:hover {
    background: white;
    border-color: var(--bs-primary);
    box-shadow: 0 5px 15px rgba(var(--bs-primary-rgb), 0.1);
}

.project-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.project-icon {
    width: 40px;
    height: 40px;
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bs-secondary);
    font-size: 1rem;
}

.project-info {
    flex: 1;
}

.project-name {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
}

.project-code {
    margin: 0;
    font-size: 0.85rem;
    color: #718096;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.role-badge.admin {
    background: var(--bs-light);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
}

.role-badge.member {
    background: var(--bs-light);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
}

.project-details {
    margin-top: 0.75rem;
}

.project-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #718096;
}

.project-description p {
    margin: 0;
    font-size: 0.9rem;
    color: #4a5568;
    line-height: 1.4;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #cbd5e0;
}

.empty-state h5 {
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #718096;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .profile-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .stat-divider {
        width: 40px;
        height: 1px;
    }
    
    .info-header {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .info-badge {
        margin-left: 0;
    }
    
    .project-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all info cards
    document.querySelectorAll('.info-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Add loading animation to buttons
    document.querySelectorAll('.btn-action').forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.classList.contains('btn-danger')) {
                return; // Don't add loading to delete button
            }
            
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Cargando...</span>';
            this.disabled = true;
            
            // Re-enable after navigation (this won't actually run due to page change)
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        });
    });
});

function confirmDelete() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
