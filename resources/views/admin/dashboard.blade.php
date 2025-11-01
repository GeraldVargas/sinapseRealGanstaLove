<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --danger: #dc3545;
            --warning: #ffc107;
        }
        
        .sinapse-navbar {
            background: linear-gradient(135deg, var(--danger) 0%, #e83e8c 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .course-popular {
            border-left: 4px solid var(--danger);
        }
    </style>
</head>
<body>
 <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/admin/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        
        <!-- Menú de navegación SIN "Dashboard" y SIN "Triggers" -->
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="/ranking/admin">
                <i class="fas fa-trophy me-1"></i>Ranking General
            </a>
            <a class="nav-link" href="{{ route('admin.cursos') }}">
                <i class="fas fa-chalkboard me-1"></i>Gestión Cursos
            </a>
            <a class="nav-link" href="{{ route('admin.usuarios') }}">
                <i class="fas fa-users me-1"></i>Gestión Usuarios
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-chart-bar me-1"></i>Reportes
            </a>
        </div>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-shield me-1"></i>{{ $usuario->Nombre }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.cursos') }}">
                        <i class="fas fa-chalkboard me-2"></i>Gestión de Cursos
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.usuarios') }}">
                        <i class="fas fa-users me-2"></i>Gestión de Usuarios
                    </a></li>
                    <li><a class="dropdown-item" href="/ranking/admin">
                        <i class="fas fa-trophy me-2"></i>Ranking General
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Header Bienvenida -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-dashboard text-white sinapse-navbar">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-5 fw-bold">¡Bienvenido, Administrador {{ $usuario->Nombre }}!</h1>
                                <p class="lead mb-0">Panel de control del sistema SINAPSE</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="bg-white rounded p-3 text-dark">
                                    <small class="text-muted">Resumen del Sistema</small>
                                    <div class="fw-bold fs-5 text-danger">{{ $total_usuarios ?? ($total_estudiantes + $total_docentes) }} Usuarios</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-primary">
                    <h3>{{ $total_estudiantes }}</h3>
                    <p class="mb-0">Estudiantes</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-success">
                    <h3>{{ $total_docentes }}</h3>
                    <p class="mb-0">Docentes</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-info">
                    <h3>{{ $total_cursos }}</h3>
                    <p class="mb-0">Cursos</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-warning">
                    <h3>{{ $total_inscripciones }}</h3>
                    <p class="mb-0">Inscripciones</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Cursos Más Populares -->
                <div class="card card-dashboard">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Cursos Más Populares</h4>
                    </div>
                    <div class="card-body">
                        @if($cursos_populares->count() > 0)
                            <div class="row">
                                @foreach($cursos_populares as $curso)
                                <div class="col-md-6 mb-3">
                                    <div class="card course-popular h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $curso->Titulo }}</h5>
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($curso->Descripcion, 80) }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $curso->inscripciones_count }} inscritos</span>
                                                <span class="badge bg-success">${{ number_format($curso->Costo, 2) }}</span>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('admin.estudiantes.curso', $curso->Id_curso) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-users me-1"></i>Ver Estudiantes
                                                </a>
                                                <a href="{{ route('admin.docentes.curso', $curso->Id_curso) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i>Ver Docentes
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay cursos disponibles.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card card-dashboard mt-4">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.cursos') }}" class="btn btn-outline-primary w-100 text-start">
                                    <i class="fas fa-chalkboard me-2"></i>Gestión de Cursos
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-outline-success w-100 text-start" data-bs-toggle="modal" data-bs-target="#modalCrearCurso">
                                    <i class="fas fa-plus me-2"></i>Crear Nuevo Curso
                                </button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-warning w-100 text-start">
                                    <i class="fas fa-users me-2"></i>Gestión de Usuarios
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-info w-100 text-start">
                                    <i class="fas fa-chart-bar me-2"></i>Reportes del Sistema
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-danger w-100 text-start">
                                    <i class="fas fa-cog me-2"></i>Configuración
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-secondary w-100 text-start">
                                    <i class="fas fa-database me-2"></i>Backup BD
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Nuevos Estudiantes -->
                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Nuevos Estudiantes</h4>
                    </div>
                    <div class="card-body">
                        @if($nuevos_estudiantes->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($nuevos_estudiantes as $estudiante)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $estudiante->Nombre }} {{ $estudiante->Apellido }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $estudiante->Email }}</small>
                                    </div>
                                    <small class="text-muted">{{ $estudiante->Fecha_registro }}</small>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted small">No hay nuevos estudiantes.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estado del Sistema -->
                <div class="card card-dashboard mt-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-server me-2"></i>Estado del Sistema</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Servidor Web</span>
                                <span class="badge bg-success">Online</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Base de Datos</span>
                                <span class="badge bg-success">Conectado</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Usuarios Activos</span>
                                <span class="badge bg-primary">{{ $total_estudiantes + $total_docentes }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Uso de CPU</span>
                                <span class="badge bg-warning">45%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Curso -->
    <div class="modal fade" id="modalCrearCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Crear Nuevo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.curso.crear') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título del Curso</label>
                            <input type="text" class="form-control" name="Titulo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="Descripcion" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duración (horas)</label>
                                <input type="number" class="form-control" name="Duracion" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Costo ($)</label>
                                <input type="number" step="0.01" class="form-control" name="Costo" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Modalidad</label>
                            <select class="form-control" name="Modalidad" required>
                                <option value="Online">Online</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Crear Curso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>