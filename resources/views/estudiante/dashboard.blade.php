<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Estudiante - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --warning: #ffc107;
            --info: #17a2b8;
            --danger: #dc3545;
        }
        
        .sinapse-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            border-left: 4px solid transparent;
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
        
        .insignia-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1rem;
            border: 2px solid #ffc107;
        }
        
        .course-progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .puntos-realtime {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffc107;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .quick-action-btn {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .quick-action-btn:hover {
            border-left: 3px solid var(--primary);
            background-color: #f8f9fa;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="/ranking">
                <i class="fas fa-trophy me-1"></i>Ranking
            </a>
            <!-- CAMBIADO: Recompensas por Canjear Recompensas -->
            <a class="nav-link" href="{{ route('estudiante.recompensas') }}">
                <i class="fas fa-gift me-1"></i>Canjear Recompensas
            </a>
            <a class="nav-link position-relative" href="{{ route('estudiante.explorar_cursos') }}">
                <i class="fas fa-search me-1"></i>Explorar Cursos
                @if($cursos_disponibles->count() > 0)
                <span class="notification-badge">{{ $cursos_disponibles->count() }}</span>
                @endif
            </a>
        </div>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="user-avatar d-inline-flex me-2">
                        {{ substr($usuario->Nombre, 0, 1) }}{{ substr($usuario->Apellido, 0, 1) }}
                    </div>
                    {{ $usuario->Nombre }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                    <!-- CAMBIADO: Recompensas por Canjear Recompensas -->
                    <li><a class="dropdown-item" href="{{ route('estudiante.recompensas') }}"><i class="fas fa-gift me-2"></i>Canjear Recompensas</a></li>
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
                            <h1 class="display-5 fw-bold">¡Bienvenido, {{ $usuario->Nombre }}!</h1>
                            <p class="lead mb-0">Continúa tu viaje de aprendizaje en SINAPSE</p>
                            <div class="mt-2">
                                <span class="badge bg-light text-primary me-2">
                                    <i class="fas fa-book me-1"></i>{{ $progreso_general['total_cursos'] }} Cursos
                                </span>
                                <span class="badge bg-light text-success me-2">
                                    <i class="fas fa-check me-1"></i>{{ $progreso_general['cursos_completados'] }} Completados
                                </span>
                                <span class="badge bg-light text-warning">
                                    <i class="fas fa-star me-1"></i>Nivel {{ $progreso_general['promedio_progreso'] > 50 ? 'Avanzado' : 'Intermedio' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-white rounded p-3 text-dark shadow">
                                <small class="text-muted">Tus Puntos Totales</small>
                                <div class="puntos-realtime" id="puntos-header">
                                    @if($gestion_puntos)
                                        {{ number_format($gestion_puntos->Total_puntos_acumulados) }}
                                    @else
                                        0
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-sync-alt me-1"></i>Actualizados en tiempo real
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
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

    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- SECCIÓN PRINCIPAL: MIS CURSOS INSCRITOS -->
            <div class="card card-dashboard" id="mis-cursos" style="border-left: 4px solid #667eea;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book me-2"></i>Mis Cursos Inscritos
                    </h4>
                    <div>
                        <span class="badge bg-light text-primary me-2">{{ $cursos_inscritos->count() }} Cursos</span>
                        @if($cursos_disponibles->count() > 0)
                        <a href="{{ route('estudiante.explorar_cursos') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>Explorar {{ $cursos_disponibles->count() }} Nuevos Cursos
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($cursos_inscritos->count() > 0)
                        <div class="row">
                            @foreach($cursos_inscritos as $curso)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-primary shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title text-primary">{{ $curso->Titulo }}</h5>
                                            <span class="badge bg-primary">Nivel {{ $curso->Nivel ?? 1 }}</span>
                                        </div>
                                        
                                        <p class="card-text text-muted small mb-3">
                                            {{ Str::limit($curso->Descripcion, 100) }}
                                        </p>
                                        
                                        <!-- Barra de Progreso -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Progreso del curso</small>
                                                <small class="text-muted fw-bold">{{ $curso->Porcentaje ?? 0 }}%</small>
                                            </div>
                                            <div class="progress course-progress">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $curso->Porcentaje ?? 0 }}%"
                                                     role="progressbar"
                                                     aria-valuenow="{{ $curso->Porcentaje ?? 0 }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="course-stats mb-3">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <small class="text-muted">Módulos</small>
                                                    <div class="fw-bold text-primary">{{ $curso->total_modulos ?? 0 }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Evaluaciones</small>
                                                    <div class="fw-bold text-warning">{{ $curso->total_evaluaciones ?? 0 }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <small class="text-muted">Duración</small>
                                                    <div class="fw-bold text-info">{{ $curso->Duracion ?? 0 }}h</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <a href="{{ route('estudiante.curso.ver', $curso->Id_curso) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-play-circle me-1"></i>Continuar Curso
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aún no estás inscrito en ningún curso</h5>
                            <p class="text-muted small mb-4">Descubre nuevos cursos y comienza tu aprendizaje.</p>
                            <a href="{{ route('estudiante.explorar_cursos') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Explorar Cursos Disponibles
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Evaluaciones Pendientes -->
@if(count($evaluaciones_pendientes) > 0)
<div class="card card-dashboard" id="evaluaciones-pendientes">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Evaluaciones Pendientes
            <span class="badge bg-danger ms-2">{{ count($evaluaciones_pendientes) }}</span>
        </h4>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($evaluaciones_pendientes as $evaluacion)
            <div class="col-md-6 mb-3">
                <div class="card h-100" style="border-left: 4px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="card-title">{{ $evaluacion->Tipo }}</h6>
                        <p class="card-text small text-muted mb-1">
                            <strong>Curso:</strong> {{ $evaluacion->curso_titulo }}
                        </p>
                        <p class="card-text small text-muted mb-2">
                            <strong>Módulo:</strong> {{ $evaluacion->modulo_nombre }}
                        </p>
                        
                        <div class="evaluation-info mb-3">
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="fas fa-star me-1"></i>{{ $evaluacion->Puntaje_maximo }} pts</span>
                                <span><i class="fas fa-clock me-1"></i>Vence: {{ \Carbon\Carbon::parse($evaluacion->Fecha_fin)->format('d/m H:i') }}</span>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('estudiante.evaluacion.ver', $evaluacion->Id_evaluacion) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-play me-1"></i>Comenzar Evaluación
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

            <!-- Progreso General -->
            <div class="card card-dashboard">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Mi Progreso General</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-primary">
                                <h3>{{ $progreso_general['total_cursos'] }}</h3>
                                <p class="mb-0">Cursos Inscritos</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-success">
                                <h3>{{ $progreso_general['cursos_completados'] }}</h3>
                                <p class="mb-0">Cursos Completados</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-warning">
                                <h3>{{ $progreso_general['total_modulos'] }}</h3>
                                <p class="mb-0">Módulos Activos</p>
                            </div>
                        </div>
                        <!-- MODIFICADO: Puntos Actuales ahora muestra Total_puntos_actual -->
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-danger">
                                <h3 id="puntos-statcard">
                                    @if($gestion_puntos)
                                        {{ number_format($gestion_puntos->Total_puntos_actual) }}
                                    @else
                                        0
                                    @endif
                                </h3>
                                <p class="mb-0">Puntos Actuales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Sistema de Puntos -->
            @if($gestion_puntos)
            <div class="card card-dashboard">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-coins me-2"></i>Mis Puntos</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h3 class="text-warning" id="puntos-sidebar">
                            @if($gestion_puntos)
                                {{ number_format($gestion_puntos->Total_puntos_acumulados) }}
                            @else
                                0
                            @endif
                        </h3>
                        <small class="text-muted">Puntos Totales</small>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Puntos Disponibles</small>
                        <div class="h5 text-primary" id="puntos-actuales">
                            @if($gestion_puntos)
                                {{ number_format($gestion_puntos->Total_puntos_actual) }}
                            @else
                                0
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <!-- CAMBIADO: Enlace a Canjear Recompensas -->
                        <a href="{{ route('estudiante.recompensas') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-gift me-2"></i>Canjear Recompensas
                        </a>
                    </div>

                    @if($ranking_actual)
                    <div class="text-center mt-3 pt-3 border-top">
                        <small class="text-muted">Tu Ranking Mensual</small>
                        <h4 class="text-primary">#{{ $ranking_actual->Posicion }}</h4>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Acciones Rápidas -->
            <div class="card card-dashboard">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h4>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('estudiante.explorar_cursos') }}" class="list-group-item list-group-item-action quick-action-btn">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <span>Explorar Cursos</span>
                                </div>
                                @if($cursos_disponibles->count() > 0)
                                <span class="badge bg-success rounded-pill">{{ $cursos_disponibles->count() }}</span>
                                @endif
                            </div>
                        </a>
                        
                        @if($cursos_inscritos->count() > 0)
                        <a href="#mis-cursos" class="list-group-item list-group-item-action quick-action-btn">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-book text-primary me-2"></i>
                                    <span>Mis Cursos</span>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $cursos_inscritos->count() }}</span>
                            </div>
                        </a>
                        @endif
                        
                        @if($evaluaciones_pendientes->count() > 0)
                        <a href="#evaluaciones-pendientes" class="list-group-item list-group-item-action quick-action-btn">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tasks text-warning me-2"></i>
                                    <span>Evaluaciones</span>
                                </div>
                                <span class="badge bg-warning rounded-pill">{{ $evaluaciones_pendientes->count() }}</span>
                            </div>
                        </a>
                        @endif
                        
                        <a href="/ranking" class="list-group-item list-group-item-action quick-action-btn">
                            <i class="fas fa-trophy text-info me-2"></i>
                            <span>Ver Ranking</span>
                        </a>
                        
                        <!-- CAMBIADO: Recompensas por Canjear Recompensas -->
                        <a href="{{ route('estudiante.recompensas') }}" class="list-group-item list-group-item-action quick-action-btn">
                            <i class="fas fa-gift text-warning me-2"></i>
                            <span>Canjear Recompensas</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Próximas Actividades -->
            <div class="card card-dashboard">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar me-2"></i>Próximas Actividades</h4>
                </div>
                <div class="card-body">
                    @if($evaluaciones_pendientes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($evaluaciones_pendientes->take(5) as $actividad)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="flex-grow-1">
                                    <small class="text-primary d-block">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($actividad->Fecha_fin)->format('d M') }}
                                    </small>
                                    <p class="mb-0 small text-truncate">{{ $actividad->Tipo }}</p>
                                    <small class="text-muted">{{ Str::limit($actividad->curso_titulo, 25) }}</small>
                                </div>
                                <span class="badge bg-warning text-dark ms-2">
                                    {{ \Carbon\Carbon::parse($actividad->Fecha_fin)->format('H:i') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">No hay actividades pendientes</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Insignias -->
            @if($insignias->count() > 0)
            <div class="card card-dashboard">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(45deg, #ffd700, #ffed4e); color: #333;">
                    <h4 class="mb-0"><i class="fas fa-trophy me-2"></i>Mis Insignias</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($insignias->take(4) as $insignia)
                        <div class="col-6 mb-3">
                            <div class="insignia-card">
                                <i class="fas fa-medal fa-2x mb-2"></i>
                                <h6 class="mb-1 small">{{ $insignia->Nombre }}</h6>
                                <small class="text-muted">+{{ $insignia->Valor_Puntos }} pts</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($insignias->count() > 4)
                    <div class="text-center">
                        <small class="text-muted">+{{ $insignias->count() - 4 }} más insignias</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Actualizar puntos en tiempo real
    function actualizarPuntos() {
        $.get('/estudiante/puntos-actuales', function(data) {
            $('#puntos-header').text(data.puntos_acumulados.toLocaleString());
            $('#puntos-sidebar').text(data.puntos_acumulados.toLocaleString());
            $('#puntos-statcard').text(data.puntos_actuales.toLocaleString());
            $('#puntos-actuales').text(data.puntos_actuales.toLocaleString());
        }).fail(function(xhr, status, error) {
            console.error('Error al actualizar puntos:', error);
        });
    }
    
    // Actualizar inmediatamente y luego cada 30 segundos
    actualizarPuntos();
    setInterval(actualizarPuntos, 30000);

    // Smooth scroll para secciones internas
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target === '#') return;
        
        $('html, body').animate({
            scrollTop: $(target).offset().top - 80
        }, 800);
    });
});

// Función para actualización en tiempo real
function actualizarPuntosTiempoReal() {
    fetch('/estudiante/puntos-actuales')
        .then(response => response.json())
        .then(data => {
            // Actualizar todos los elementos que muestran puntos
            document.getElementById('puntos-header').textContent = data.puntos_acumulados.toLocaleString();
            document.getElementById('puntos-sidebar').textContent = data.puntos_acumulados.toLocaleString();
            document.getElementById('puntos-statcard').textContent = data.puntos_actuales.toLocaleString();
            document.getElementById('puntos-actuales').textContent = data.puntos_actuales.toLocaleString();
            
            // Mostrar notificación si hay cambios
            if (window.puntosAnteriors !== data.puntos_acumulados) {
                if (window.puntosAnteriors !== undefined) {
                    mostrarNotificacionPuntos(data.puntos_acumulados - window.puntosAnteriors);
                }
                window.puntosAnteriors = data.puntos_acumulados;
            }
        })
        .catch(error => console.error('Error actualizando puntos:', error));
}

function mostrarNotificacionPuntos(puntosGanados) {
    if (puntosGanados > 0) {
        // Crear notificación bonita
        const notificacion = document.createElement('div');
        notificacion.className = 'position-fixed top-0 end-0 m-4 p-3 bg-success text-white rounded shadow-lg';
        notificacion.style.zIndex = '1060';
        notificacion.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-coins fa-2x me-3"></i>
                <div>
                    <h6 class="mb-1">¡Puntos Ganados!</h6>
                    <p class="mb-0">+${puntosGanados} puntos</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notificacion);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notificacion.remove();
        }, 3000);
    }
}

// Actualizar cada 5 segundos
setInterval(actualizarPuntosTiempoReal, 5000);
// Actualizar inmediatamente al cargar
actualizarPuntosTiempoReal();
</script>
</body>
</html>