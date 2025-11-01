<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Docente - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --success: #28a745;
            --info: #17a2b8;
        }
        
        .sinapse-navbar {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
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
        
        .course-card {
            border-left: 4px solid var(--success);
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/docente/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        
        <!-- Menú de navegación SIN "Dashboard" -->
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="/ranking/docente">
                <i class="fas fa-trophy me-1"></i>Ranking Estudiantes
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-chalkboard me-1"></i>Mis Cursos
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-file-alt me-1"></i>Evaluaciones
            </a>
        </div>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>{{ $usuario->Nombre }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                    <li><a class="dropdown-item" href="/ranking/docente"><i class="fas fa-trophy me-2"></i>Ver Ranking</a></li>
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
        <!-- Header Bienvenida -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-dashboard text-white sinapse-navbar">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-5 fw-bold">¡Bienvenido, Docente {{ $usuario->Nombre }}!</h1>
                                <p class="lead mb-0">Panel de gestión docente</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="bg-white rounded p-3 text-dark">
                                    <small class="text-muted">Estadísticas</small>
                                    <div class="fw-bold fs-5 text-success">{{ $total_cursos }} Cursos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stat-card bg-primary">
                    <h3>{{ $total_cursos }}</h3>
                    <p class="mb-0">Cursos Asignados</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card bg-success">
                    <h3>{{ $total_estudiantes }}</h3>
                    <p class="mb-0">Estudiantes Totales</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card bg-warning">
                    <h3>{{ $evaluaciones_pendientes }}</h3>
                    <p class="mb-0">Evaluaciones</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Mis Cursos -->
            <div class="col-lg-8">
                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Mis Cursos Asignados</h4>
                    </div>
                    <div class="card-body">
                        @if($cursos_asignados->count() > 0)
                            <div class="row">
                                @foreach($cursos_asignados as $curso)
                                <div class="col-md-6 mb-4">
                                    <div class="card course-card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $curso->Titulo }}</h5>
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($curso->Descripcion, 100) }}
                                            </p>
                                            <div class="mb-3">
                                                <span class="badge bg-primary me-2">{{ $curso->Duracion }}h</span>
                                                <span class="badge bg-info">${{ number_format($curso->Costo, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $curso->Estado ? 'Activo' : 'Inactivo' }}
                                                </small>
                                                <div>
                                                    <a href="{{ route('docente.curso.detalle', $curso->Id_curso) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-eye me-1"></i>Gestionar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tienes cursos asignados.</p>
                                <p class="text-muted small">Contacta al administrador para que te asigne cursos.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="col-lg-4">
                <!-- Crear Evaluación -->
                <div class="card card-dashboard">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Acciones Rápidas</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm text-start" data-bs-toggle="modal" data-bs-target="#modalEvaluacion">
                                <i class="fas fa-file-alt me-2"></i>Crear Evaluación
                            </button>
                            <button class="btn btn-outline-success btn-sm text-start">
                                <i class="fas fa-tasks me-2"></i>Ver Calificaciones
                            </button>
                            <button class="btn btn-outline-warning btn-sm text-start">
                                <i class="fas fa-chart-bar me-2"></i>Reportes de Progreso
                            </button>
                            <button class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-users me-2"></i>Gestionar Estudiantes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Próximas Evaluaciones -->
                <div class="card card-dashboard mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-calendar me-2"></i>Próximas Evaluaciones</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">Hoy</small>
                                    <p class="mb-0 small">Examen Parcial - Matemáticas</p>
                                </div>
                                <span class="badge bg-warning">15:00</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">Mañana</small>
                                    <p class="mb-0 small">Quiz - Programación</p>
                                </div>
                                <span class="badge bg-info">10:00</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">15 Mar</small>
                                    <p class="mb-0 small">Proyecto Final</p>
                                </div>
                                <span class="badge bg-danger">23:59</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Modal Crear Evaluación -->
<div class="modal fade" id="modalEvaluacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Crear Nueva Evaluación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEvaluacion" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Curso</label>
                        <select class="form-control" name="id_curso" id="selectCurso" required>
                            <option value="">Seleccionar curso...</option>
                            @foreach($cursos_asignados as $curso)
                            <option value="{{ $curso->Id_curso }}">{{ $curso->Titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Campo: Seleccionar módulo -->
                    <div class="mb-3">
                        <label class="form-label">Módulo</label>
                        <select class="form-control" name="Id_modulo" id="selectModulo" required disabled>
                            <option value="">Primero selecciona un curso</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Evaluación</label>
                        <select class="form-control" name="tipo" required>
                            <option value="Examen">Examen</option>
                            <option value="Cuestionario">Cuestionario</option>
                            <option value="Proyecto">Proyecto</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Puntaje Máximo</label>
                        <input type="number" class="form-control" name="puntaje_maximo" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="datetime-local" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="datetime-local" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Evaluación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formEvaluacion = document.getElementById('formEvaluacion');
    const selectCurso = document.getElementById('selectCurso');
    const selectModulo = document.getElementById('selectModulo');
    
    // Cargar módulos cuando se selecciona un curso
    selectCurso.addEventListener('change', function() {
        const cursoId = this.value;
        
        if (!cursoId) {
            selectModulo.innerHTML = '<option value="">Primero selecciona un curso</option>';
            selectModulo.disabled = true;
            return;
        }
        
        // Hacer petición para obtener módulos del curso
        fetch(`/docente/modulos-por-curso/${cursoId}`)
            .then(response => response.json())
            .then(data => {
                selectModulo.innerHTML = '<option value="">Seleccionar módulo...</option>';
                data.forEach(modulo => {
                    selectModulo.innerHTML += `<option value="${modulo.Id_modulo}">${modulo.Nombre}</option>`;
                });
                selectModulo.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                selectModulo.innerHTML = '<option value="">Error al cargar módulos</option>';
            });
    });
    
    formEvaluacion.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cursoId = selectCurso.value;
        if (!cursoId) {
            alert('Por favor selecciona un curso');
            return;
        }
        
        // Actualizar la acción del formulario con el ID del curso
        formEvaluacion.action = `/docente/evaluacion/crear/${cursoId}`;
        formEvaluacion.submit();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formEvaluacion = document.getElementById('formEvaluacion');
    const selectCurso = document.getElementById('selectCurso');
    const selectTema = document.getElementById('selectTema');
    
    // Cargar temas cuando se selecciona un curso
    selectCurso.addEventListener('change', function() {
        const cursoId = this.value;
        
        if (!cursoId) {
            selectTema.innerHTML = '<option value="">Primero selecciona un curso</option>';
            selectTema.disabled = true;
            return;
        }
        
        // Hacer petición para obtener temas del curso
        fetch(`/docente/temas-por-curso/${cursoId}`)
            .then(response => response.json())
            .then(data => {
                selectTema.innerHTML = '<option value="">Seleccionar tema...</option>';
                data.forEach(tema => {
                    selectTema.innerHTML += `<option value="${tema.Id_tema}">${tema.Nombre}</option>`;
                });
                selectTema.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                selectTema.innerHTML = '<option value="">Error al cargar temas</option>';
            });
    });
    
    formEvaluacion.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cursoId = selectCurso.value;
        if (!cursoId) {
            alert('Por favor selecciona un curso');
            return;
        }
        
        // Actualizar la acción del formulario con el ID del curso
        formEvaluacion.action = `/docente/evaluacion/crear/${cursoId}`;
        formEvaluacion.submit();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formEvaluacion = document.getElementById('formEvaluacion');
    const selectCurso = document.getElementById('selectCurso');
    
    formEvaluacion.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cursoId = selectCurso.value;
        if (!cursoId) {
            alert('Por favor selecciona un curso');
            return;
        }
        
        // Actualizar la acción del formulario con el ID del curso
        formEvaluacion.action = `/docente/evaluacion/crear/${cursoId}`;
        formEvaluacion.submit();
    });
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>