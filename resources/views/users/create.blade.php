@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
<!-- Header Section with Gradient Background -->
<div class="bg-gradient-primary text-white rounded-4 p-4 mb-4 shadow-custom">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <div>
                    <h1 class="h2 fw-bold mb-1">Crear Nuevo Usuario</h1>
                    <p class="mb-0 opacity-90">Agrega un nuevo usuario al sistema</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="bg-white bg-opacity-10 rounded-3 p-3">
                <i class="fas fa-users fa-2x mb-2"></i>
                <div class="small">Sistema de Gestión</div>
            </div>
        </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb breadcrumb-dark mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('users.index') }}" class="text-white text-decoration-none">
                    <i class="fas fa-users me-1"></i>Usuarios
                </a>
            </li>
            <li class="breadcrumb-item active text-white-50">Crear</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="createUserForm">
            @csrf
            
            <!-- Progress Indicator -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="step-indicator active" data-step="1">
                                <div class="step-circle">
                                    <i class="fas fa-user"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Información Personal</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="step-indicator" data-step="2">
                                <div class="step-circle">
                                    <i class="fas fa-key"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Cuenta y Acceso</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="step-indicator" data-step="3">
                                <div class="step-circle">
                                    <i class="fas fa-image"></i>
                                </div>
                                <small class="d-block mt-2 fw-medium">Foto de Perfil</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Personal Information -->
            <div class="card border-0 shadow-sm mb-4 form-step" id="step-1">
                <div class="card-header bg-gradient-accent text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Información Personal</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-floating">
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Nombre"
                                       required>
                                <label for="name">
                                    <i class="fas fa-user me-2 text-primary"></i>Nombre <span class="text-danger">*</span>
                                </label>
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-floating">
                                <input type="text"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name"
                                       name="last_name"
                                       value="{{ old('last_name') }}"
                                       placeholder="Apellido"
                                       required>
                                <label for="last_name">
                                    <i class="fas fa-user me-2 text-primary"></i>Apellido <span class="text-danger">*</span>
                                </label>
                                @error('last_name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="form-floating">
                                <input type="number"
                                       class="form-control @error('edad') is-invalid @enderror"
                                       id="edad"
                                       name="edad"
                                       value="{{ old('edad') }}"
                                       placeholder="Edad"
                                       min="1"
                                       max="120">
                                <label for="edad">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Edad
                                </label>
                                @error('edad')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="form-floating">
                                <select class="form-select @error('genero') is-invalid @enderror"
                                        id="genero"
                                        name="genero">
                                    <option value="">Seleccionar...</option>
                                    <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                <label for="genero">
                                    <i class="fas fa-venus-mars me-2 text-primary"></i>Género
                                </label>
                                @error('genero')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="form-floating">
                                <input type="text"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono"
                                       name="telefono"
                                       value="{{ old('telefono') }}"
                                       placeholder="Teléfono">
                                <label for="telefono">
                                    <i class="fas fa-phone me-2 text-primary"></i>Teléfono
                                </label>
                                @error('telefono')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary btn-lg px-4" onclick="nextStep(2)">
                            Siguiente <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Account Information -->
            <div class="card border-0 shadow-sm mb-4 form-step d-none" id="step-2">
                <div class="card-header bg-gradient-accent text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <i class="fas fa-key"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Información de Cuenta</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Email"
                                   required>
                            <label for="email">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email <span class="text-danger">*</span>
                            </label>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-floating position-relative">
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Contraseña"
                                       required>
                                <label for="password">
                                    <i class="fas fa-lock me-2 text-primary"></i>Contraseña <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-3" 
                                        onclick="togglePassword('password')" style="z-index: 10;">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                                @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-floating position-relative">
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Confirmar Contraseña"
                                       required>
                                <label for="password_confirmation">
                                    <i class="fas fa-lock me-2 text-primary"></i>Confirmar Contraseña <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-3" 
                                        onclick="togglePassword('password_confirmation')" style="z-index: 10;">
                                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-floating">
                            <select class="form-select @error('role') is-invalid @enderror"
                                    id="role"
                                    name="role">
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            <label for="role">
                                <i class="fas fa-user-tag me-2 text-primary"></i>Rol del Usuario
                            </label>
                            @error('role')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left me-2"></i>Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" onclick="nextStep(3)">
                            Siguiente <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Profile Photo -->
            <div class="card border-0 shadow-sm mb-4 form-step d-none" id="step-3">
                <div class="card-header bg-gradient-accent text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <i class="fas fa-image"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Foto de Perfil</h5>
                    </div>
                </div>
                <div class="card-body p-4 text-center">
                    <!-- Image Upload Area -->
                    <div class="upload-area border-2 border-dashed border-primary rounded-4 p-5 mb-4 position-relative" 
                         onclick="document.getElementById('uri').click()" 
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)">
                        
                        <div id="upload-placeholder">
                            <div class="text-primary mb-3">
                                <i class="fas fa-cloud-upload-alt fa-4x"></i>
                            </div>
                            <h6 class="fw-bold mb-2">Arrastra y suelta tu imagen aquí</h6>
                            <p class="text-muted mb-3">o haz clic para seleccionar un archivo</p>
                            <div class="badge bg-light text-dark">JPG, PNG, GIF - Máx. 5MB</div>
                        </div>

                        <div id="imagePreview" class="d-none">
                            <img id="preview" src="" alt="Vista previa" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px;">
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeImage()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Imagen
                                </button>
                            </div>
                        </div>

                        <input type="file"
                               class="d-none @error('uri') is-invalid @enderror"
                               id="uri"
                               name="uri"
                               accept="image/*"
                               onchange="previewImage(event)">
                    </div>

                    @error('uri')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                        </div>
                    @enderror

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left me-2"></i>Anterior
                        </button>
                        <button type="submit" class="btn btn-success btn-lg px-4" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Crear Usuario
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <small class="text-muted">¿Necesitas ayuda? Consulta la guía de usuario</small>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Step Indicator Styles */
.step-indicator {
    position: relative;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step-indicator.active {
    opacity: 1;
}

.step-indicator.completed {
    opacity: 1;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--bs-light);
    border: 3px solid var(--bs-border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: all 0.3s ease;
    color: var(--bs-secondary);
}

.step-indicator.active .step-circle {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.step-indicator.completed .step-circle {
    background: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

/* Upload Area Styles */
.upload-area {
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bs-light);
}

.upload-area:hover {
    border-color: var(--accent-color) !important;
    background: var(--bs-primary-bg-subtle);
    transform: translateY(-2px);
}

.upload-area.drag-over {
    border-color: var(--success-color) !important;
    background: var(--bs-success-bg-subtle);
    transform: scale(1.02);
}

/* Form Floating Enhancements */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label {
    color: var(--primary-color);
}

.form-floating > .form-control:focus,
.form-floating > .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
}

/* Password Toggle Button */
.btn-link {
    color: var(--bs-secondary);
    text-decoration: none;
    border: none;
    background: none;
}

.btn-link:hover {
    color: var(--primary-color);
}

/* Breadcrumb Dark Theme */
.breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.5);
}

/* Loading Animation */
.btn.loading {
    position: relative;
    color: transparent !important;
}

.btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Form Step Animation */
.form-step {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .step-indicator small {
        font-size: 0.7rem;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
    }
    
    .upload-area {
        padding: 2rem !important;
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
    const form = document.getElementById('createUserForm');
    const submitBtn = document.getElementById('submitBtn');

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Las contraseñas no coinciden');
            passwordConfirmation.classList.add('is-invalid');
        } else {
            passwordConfirmation.setCustomValidity('');
            passwordConfirmation.classList.remove('is-invalid');
        }
    }

    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        }
        form.classList.add('was-validated');
    });
});

// Step Navigation Functions
let currentStep = 1;

function nextStep(step) {
    // Validate current step
    if (!validateCurrentStep()) {
        return;
    }

    // Hide current step
    document.getElementById(`step-${currentStep}`).classList.add('d-none');

    // Show next step
    document.getElementById(`step-${step}`).classList.remove('d-none');

    // Update step indicators
    updateStepIndicators(step);

    currentStep = step;

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    // Hide current step
    document.getElementById(`step-${currentStep}`).classList.add('d-none');

    // Show previous step
    document.getElementById(`step-${step}`).classList.remove('d-none');

    // Update step indicators
    updateStepIndicators(step);

    currentStep = step;

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateStepIndicators(activeStep) {
    const indicators = document.querySelectorAll('.step-indicator');

    indicators.forEach((indicator, index) => {
        const stepNumber = index + 1;

        indicator.classList.remove('active', 'completed');

        if (stepNumber === activeStep) {
            indicator.classList.add('active');
        } else if (stepNumber < activeStep) {
            indicator.classList.add('completed');
        }
    });
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Special validation for step 2 (passwords)
    if (currentStep === 2) {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.classList.add('is-invalid');
            isValid = false;
        }
    }

    if (!isValid) {
        // Show error message
        showNotification('Por favor, completa todos los campos requeridos', 'error');
    }

    return isValid;
}

// Password Toggle Functions
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(fieldId + '-eye');

    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Image Upload Functions
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('El archivo es demasiado grande. Máximo 5MB permitido.', 'error');
            event.target.value = '';
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showNotification('Por favor selecciona un archivo de imagen válido.', 'error');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('upload-placeholder').classList.add('d-none');
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('uri').value = '';
    document.getElementById('upload-placeholder').classList.remove('d-none');
    document.getElementById('imagePreview').classList.add('d-none');
}

// Drag and Drop Functions
function handleDragOver(event) {
    event.preventDefault();
    event.currentTarget.classList.add('drag-over');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('drag-over');
}

function handleDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('drag-over');

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('uri').files = files;
        previewImage({ target: { files: files } });
    }
}

// Notification Function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush
