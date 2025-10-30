@extends('layouts.app')

@section('title', 'Listado de Usuarios')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestión de Usuarios</h1>
            <p class="mb-0 text-muted">Administra y supervisa todos los usuarios del sistema.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus fa-sm me-2"></i>Crear Nuevo Usuario
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label mb-1">Total Usuarios</div>
                        <div class="stat-value h4 mb-0">{{ $users->total() }}</div>
                    </div>
                    <div class="icon-circle" title="Total">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label mb-1">Administradores</div>
                        <div class="stat-value h4 mb-0">{{ $users->where('role', 'admin')->count() }}</div>
                    </div>
                    <div class="icon-circle danger" title="Admins">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label mb-1">Usuarios Regulares</div>
                        <div class="stat-value h4 mb-0">{{ $users->where('role', '!=', 'admin')->count() }}</div>
                    </div>
                    <div class="icon-circle secondary" title="Regulares">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label mb-1">Verificados</div>
                        <div class="stat-value h4 mb-0">{{ $users->whereNotNull('email_verified_at')->count() }}</div>
                    </div>
                    <div class="icon-circle success" title="Verificados">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-semibold">Lista de Usuarios</h6>
            <span class="badge bg-light text-dark">{{ $users->total() }} en total</span>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        @if($user->uri)
                                            <img src="{{ $user->uri }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $user->user_code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $user->name }} {{ $user->last_name }}</strong>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Verificado</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->telefono ?? 'N/A' }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger"><i class="fas fa-crown me-1"></i>Admin</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-user me-1"></i>Usuario</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles" data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar" data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $user->id }})" title="Eliminar" data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }} usuarios
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-gray-400 mb-3"></i>
                    <h4 class="fw-bold">No hay usuarios registrados</h4>
                    <p class="text-muted">Comienza creando tu primer usuario para gestionar el sistema.</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Crear Primer Usuario
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="h5">¿Estás seguro de eliminar este usuario?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar Usuario</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Delete confirmation modal logic
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let userIdToDelete = null;

    window.confirmDelete = function (userId) {
        userIdToDelete = userId;
        deleteModal.show();
    };

    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (userIdToDelete) {
            document.getElementById('delete-form-' + userIdToDelete).submit();
        }
    });
});
</script>
@endpush