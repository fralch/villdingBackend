@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<!-- Header Section -->
<div class="header-section mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Editar Usuario</h1>
                        <p class="page-subtitle mb-0">Actualiza la información del usuario {{ $user->name }}</p>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb custom-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('users.index') }}">
                                <i class="fas fa-users me-1"></i>Usuarios
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Perfil
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="form-card">
                <div class="form-header">
                    <div class="d-flex align-items-center">
                        <div class="form-icon me-3">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h4 class="form-title mb-1">Información del Usuario</h4>
                            <p class="form-subtitle mb-0">Modifica los datos según sea necesario</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <!-- Información Personal -->
                    <div class="form-section mb-5">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h5 class="section-title mb-1">Información Personal</h5>
                                    <p class="section-subtitle mb-0">Datos básicos del usuario</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Nombre"
                                           required>
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name', $user->last_name) }}"
                                           placeholder="Apellido"
                                           required>
                                    <label for="last_name">Apellido <span class="text-danger">*</span></label>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number"
                                           class="form-control @error('edad') is-invalid @enderror"
                                           id="edad"
                                           name="edad"
                                           value="{{ old('edad', $user->edad) }}"
                                           placeholder="Edad"
                                           min="1"
                                           max="120">
                                    <label for="edad">Edad</label>
                                    @error('edad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('genero') is-invalid @enderror"
                                            id="genero"
                                            name="genero">
                                        <option value="">Seleccionar...</option>
                                        <option value="Masculino" {{ old('genero', $user->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('genero', $user->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('genero', $user->genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    <label for="genero">Género</label>
                                    @error('genero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text"
                                           class="form-control @error('telefono') is-invalid @enderror"
                                           id="telefono"
                                           name="telefono"
                                           value="{{ old('telefono', $user->telefono) }}"
                                           placeholder="Teléfono">
                                    <label for="telefono">Teléfono</label>
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Cuenta -->
                    <div class="form-section mb-5">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon me-3">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <h5 class="section-title mb-1">Información de Cuenta</h5>
                                    <p class="section-subtitle mb-0">Credenciales y permisos del usuario</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="Email"
                                           required>
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-3 text-info"></i>
                                        <div>
                                            <strong>Cambio de contraseña:</strong> Deja los campos vacíos si no deseas cambiar la contraseña actual.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Nueva Contraseña"
                                           style="padding-right: 3rem;">
                                    <label for="password">Nueva Contraseña</label>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                    <small class="form-text text-muted d-block mt-2">Mínimo 6 caracteres</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirmar Nueva Contraseña"
                                           style="padding-right: 3rem;">
                                    <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('role') is-invalid @enderror"
                                            id="role"
                                            name="role">
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Usuario</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    </select>
                                    <label for="role">Rol del Usuario</label>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Foto de Perfil -->
                    <div class="form-section mb-5">
                        <div class="section-header mb-4">
                            <div class="d-flex align-items-center">
                                <div class="section-icon me-3">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div>
                                    <h5 class="section-title mb-1">Foto de Perfil</h5>
                                    <p class="section-subtitle mb-0">Imagen del usuario</p>
                                </div>
                            </div>
                        </div>

                        @if($user->uri)
                            <div class="current-image-section mb-4">
                                <label class="form-label fw-semibold mb-3">Imagen Actual</label>
                                <div class="current-image-wrapper">
                                    <img src="{{ $user->uri }}" alt="{{ $user->name }}" class="current-profile-image">
                                    <div class="image-overlay">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="upload-section">
                            <label for="uri" class="form-label fw-semibold mb-3">
                                {{ $user->uri ? 'Cambiar Imagen' : 'Subir Imagen' }}
                            </label>
                            
                            <div class="upload-area" id="uploadArea">
                                <input type="file"
                                       class="form-control d-none @error('uri') is-invalid @enderror"
                                       id="uri"
                                       name="uri"
                                       accept="image/*"
                                       onchange="previewImage(event)">
                                
                                <div class="upload-content" id="uploadContent">
                                    <div class="upload-icon mb-3">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h6 class="upload-title mb-2">Arrastra una imagen aquí</h6>
                                    <p class="upload-subtitle mb-3">o haz clic para seleccionar</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('uri').click()">
                                        <i class="fas fa-folder-open me-2"></i>Seleccionar Archivo
                                    </button>
                                </div>

                                <div class="preview-content d-none" id="previewContent">
                                    <img id="preview" src="" alt="Vista previa" class="preview-image">
                                    <div class="preview-actions">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePreview()">
                                            <i class="fas fa-trash me-1"></i>Eliminar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('uri').click()">
                                            <i class="fas fa-edit me-1"></i>Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                            </small>
                            
                            @error('uri')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span class="btn-text">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    /* Form Styling */
    .form-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
    }

    .form-header h4 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Form Sections */
    .form-section {
        position: relative;
        padding: 1.5rem 0;
    }

    .form-section:not(:last-child)::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, #e9ecef 20%, #e9ecef 80%, transparent 100%);
    }

    .section-header {
        margin-bottom: 1.5rem;
    }

    .section-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .section-title {
        color: #2d3748;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .section-subtitle {
        color: #718096;
        font-size: 0.9rem;
    }

    /* Floating Labels */
    .form-floating > label {
        color: #718096;
        font-weight: 500;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #667eea;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .form-floating > .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-floating > .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-floating > .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 0.75rem;
        transition: all 0.3s ease;
    }

    .form-floating > .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Password Toggle */
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #718096;
        cursor: pointer;
        z-index: 10;
        transition: color 0.3s ease;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #667eea;
    }

    .password-toggle:focus {
        outline: none;
        color: #667eea;
    }

    /* Current Image */
    .current-image-wrapper {
        position: relative;
        display: inline-block;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .current-image-wrapper:hover {
        transform: translateY(-5px);
    }

    .current-profile-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .current-image-wrapper:hover .image-overlay {
        opacity: 1;
    }

    /* Upload Area */
    .upload-area {
        border: 3px dashed #cbd5e0;
        border-radius: 15px;
        padding: 3rem 2rem;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .upload-area.dragover {
        border-color: #667eea;
        background: #e6f3ff;
        transform: scale(1.02);
    }

    .upload-icon {
        font-size: 3rem;
        color: #a0aec0;
        margin-bottom: 1rem;
    }

    .upload-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .upload-subtitle {
        color: #718096;
        margin-bottom: 1.5rem;
    }

    /* Preview */
    .preview-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .preview-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .preview-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn-lg {
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }

    .btn-outline-secondary {
        border: 2px solid #e2e8f0;
        color: #718096;
    }

    .btn-outline-secondary:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
        color: #4a5568;
        transform: translateY(-2px);
    }

    /* Loading Animation */
    .btn-loading {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-header {
            padding: 1.5rem;
            margin: -1rem -1rem 1.5rem -1rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .upload-area {
            padding: 2rem 1rem;
        }

        .form-actions .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .btn-lg {
            width: 100%;
        }
    }

    /* Animation */
    .form-section {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    .form-section:nth-child(3) { animation-delay: 0.3s; }
    .form-section:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
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

    // Form validation
    const form = document.getElementById('editUserForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Password confirmation validation
    const newPassword = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (newPassword.value && confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                confirmPassword.classList.add('is-invalid');
                return false;
            } else {
                confirmPassword.setCustomValidity('');
                confirmPassword.classList.remove('is-invalid');
                return true;
            }
        }
        return true;
    }

    if (newPassword && confirmPassword) {
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!validatePasswords()) {
            e.preventDefault();
            return;
        }

        // Show loading state
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;

        // Simulate form processing (remove this in production)
        setTimeout(() => {
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            submitBtn.disabled = false;
        }, 2000);
    });

    // Password toggle functionality
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Image upload functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('uri');
    const uploadContent = document.getElementById('uploadContent');
    const previewContent = document.getElementById('previewContent');
    const preview = document.getElementById('preview');

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            previewImage({ target: fileInput });
        }
    });

    uploadArea.addEventListener('click', function(e) {
        if (e.target === uploadArea || e.target.closest('.upload-content')) {
            fileInput.click();
        }
    });
});

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    const uploadContent = document.getElementById('uploadContent');
    const previewContent = document.getElementById('previewContent');
    const preview = document.getElementById('preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            uploadContent.classList.add('d-none');
            previewContent.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

// Remove preview function
function removePreview() {
    const fileInput = document.getElementById('uri');
    const uploadContent = document.getElementById('uploadContent');
    const previewContent = document.getElementById('previewContent');
    
    fileInput.value = '';
    uploadContent.classList.remove('d-none');
    previewContent.classList.add('d-none');
}

// Custom notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Password Toggle Function
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
