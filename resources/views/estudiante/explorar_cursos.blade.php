<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar Cursos - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
        }
        
        .sinapse-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .course-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            border-left: 4px solid transparent;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .btn-inscribirse {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-inscribirse:hover {
            background: linear-gradient(45deg, #218838, #1e9e8a);
            transform: translateY(-2px);
        }
        
        .badge-inscrito {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .course-available {
            border-left-color: var(--success);
        }

        .course-enrolled {
            border-left-color: var(--primary);
        }

        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="user-avatar d-inline-flex me-2">
                        {{ substr($usuario->Nombre, 0, 1) }}{{ substr($usuario->Apellido, 0, 1) }}
                    </div>
                    {{ $usuario->Nombre }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/estudiante/dashboard"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid py-4">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold text-primary">
                            <i class="fas fa-search me-2"></i>Explorar Cursos
                        </h1>
                        <p class="text-muted">Descubre todos los cursos disponibles en SINAPSE</p>
                    </div>
                    <a href="/estudiante/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Resumen y Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-2">Filtrar cursos:</h6>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all">
                                        Todos
                                    </button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="disponibles">
                                        Disponibles
                                    </button>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="inscritos">
                                        Mis Cursos
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-3 justify-content-end">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-book me-1"></i>Total: {{ $todos_cursos->count() }}
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="fas fa-plus me-1"></i>Disponibles: {{ $cursos_disponibles->count() }}
                                    </span>
                                    <span class="badge badge-inscrito">
                                        <i class="fas fa-check me-1"></i>Inscritos: {{ $cursos_inscritos->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cursos Disponibles -->
        @if($cursos_disponibles->count() > 0)
        <div class="row mb-5 curso-section" id="section-disponibles">
            <div class="col-12">
                <h3 class="h4 text-success mb-3">
                    <i class="fas fa-plus-circle me-2"></i>Cursos Disponibles para Inscripción
                    <span class="badge bg-success ms-2">{{ $cursos_disponibles->count() }}</span>
                </h3>
                
                <div class="row">
                    @foreach($cursos_disponibles as $curso)
                    <div class="col-md-6 col-lg-4 mb-4 curso-card" data-type="disponible">
                        <div class="card course-card course-available h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success">{{ $curso->Titulo }}</h5>
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($curso->Descripcion, 120) }}
                                </p>
                                
                                <div class="course-meta mb-3">
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span><i class="fas fa-clock me-1"></i>{{ $curso->Duracion }}h</span>
                                        <span><i class="fas fa-users me-1"></i>{{ $curso->total_estudiantes }} estudiantes</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><i class="fas fa-layer-group me-1"></i>{{ $curso->total_modulos }} módulos</span>
                                        <span><i class="fas fa-tasks me-1"></i>{{ $curso->total_evaluaciones }} evaluaciones</span>
                                    </div>
                                </div>

                                <div class="course-price mb-3">
                                    @if($curso->Costo > 0)
                                    <h5 class="text-success">${{ number_format($curso->Costo, 2) }}</h5>
                                    @else
                                    <h5 class="text-success">Gratuito</h5>
                                    @endif
                                </div>

                                <!-- BOTÓN DE INSCRIPCIÓN -->
                                <div class="d-grid">
                                    <form action="{{ route('estudiante.inscribirse', $curso->Id_curso) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-inscribirse w-100">
                                            <i class="fas fa-plus-circle me-2"></i>Inscribirse al Curso
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Mis Cursos Inscritos -->
        @if($cursos_inscritos->count() > 0)
        <div class="row curso-section" id="section-inscritos">
            <div class="col-12">
                <h3 class="h4 text-primary mb-3">
                    <i class="fas fa-check-circle me-2"></i>Mis Cursos Inscritos
                    <span class="badge bg-primary ms-2">{{ $cursos_inscritos->count() }}</span>
                </h3>
                
                <div class="row">
                    @foreach($cursos_inscritos as $curso)
                    <div class="col-md-6 col-lg-4 mb-4 curso-card" data-type="inscrito">
                        <div class="card course-card course-enrolled h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title text-primary">{{ $curso->Titulo }}</h5>
                                    <span class="badge badge-inscrito">Inscrito</span>
                                </div>
                                
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($curso->Descripcion, 100) }}
                                </p>
                                
                                <div class="course-meta mb-3">
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><i class="fas fa-layer-group me-1"></i>{{ $curso->total_modulos }} módulos</span>
                                        <span><i class="fas fa-tasks me-1"></i>{{ $curso->total_evaluaciones }} evaluaciones</span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('estudiante.curso.ver', $curso->Id_curso) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-play-circle me-2"></i>Continuar Curso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Sin cursos disponibles -->
        @if($cursos_disponibles->count() == 0 && $cursos_inscritos->count() == 0)
        <div class="text-center py-5">
            <i class="fas fa-book fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay cursos disponibles</h4>
            <p class="text-muted">Vuelve más tarde para ver nuevos cursos.</p>
            <a href="/estudiante/dashboard" class="btn btn-primary mt-3">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
        @elseif($cursos_disponibles->count() == 0 && $cursos_inscritos->count() > 0)
        <div class="text-center py-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>¡Estás en todos los cursos disponibles!</strong>
                <p class="mb-0 mt-1">Continúa con tus cursos actuales para ganar más puntos y experiencia.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Filtrado de cursos
    $('.filter-btn').click(function() {
        var filter = $(this).data('filter');
        
        // Actualizar botones activos
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Mostrar/ocultar cursos según el filtro
        if (filter === 'all') {
            $('.curso-card').show();
            $('.curso-section').show();
        } else if (filter === 'disponibles') {
            $('.curso-card').hide();
            $('[data-type="disponible"]').show();
            $('#section-disponibles').show();
            $('#section-inscritos').hide();
        } else if (filter === 'inscritos') {
            $('.curso-card').hide();
            $('[data-type="inscrito"]').show();
            $('#section-disponibles').hide();
            $('#section-inscritos').show();
        }
    });

    // Confirmación para inscripción
    $('form[action*="inscribirse"]').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var cursoNombre = form.closest('.card').find('.card-title').text();
        
        if (confirm('¿Estás seguro de que quieres inscribirte en el curso "' + cursoNombre + '"?')) {
            form.off('submit').submit();
        }
    });
});
</script>
</body>
</html>