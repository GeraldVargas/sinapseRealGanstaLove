<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Estudiante - {{ $estudiante->Nombre }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .student-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            color: white;
            margin-bottom: 1rem;
        }
        .progress-thin {
            height: 8px;
        }
        .badge-custom {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        .student-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/docente/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('docente.curso.detalle', $curso->Id_curso) }}">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Curso
                </a>
            </div>
        </div>
    </nav>

    <!-- Header del Estudiante -->
    <div class="student-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/docente/dashboard" class="text-white">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('docente.curso.detalle', $curso->Id_curso) }}" class="text-white">{{ $curso->Titulo }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $estudiante->Nombre }}</li>
                        </ol>
                    </nav>
                    
                    <div class="d-flex align-items-center">
                        <div class="student-avatar me-4">
                            {{ substr($estudiante->Nombre, 0, 1) }}{{ substr($estudiante->Apellido, 0, 1) }}
                        </div>
                        <div>
                            <h1 class="display-5 fw-bold">{{ $estudiante->Nombre }} {{ $estudiante->Apellido }}</h1>
                            <p class="lead mb-1">{{ $estudiante->Email }}</p>
                            <div class="mt-2">
                                <span class="badge bg-light text-primary me-2">
                                    <i class="fas fa-id-card me-1"></i>ID: {{ $estudiante->Id_usuario }}
                                </span>
                                <span class="badge bg-light text-success">
                                    <i class="fas fa-calendar me-1"></i>Registro: {{ \Carbon\Carbon::parse($estudiante->Fecha_registro)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white rounded p-3 text-dark shadow">
                        <small class="text-muted">Progreso en el Curso</small>
                        <div class="h3 text-primary mb-1">{{ $progreso->Porcentaje ?? 0 }}%</div>
                        <div class="progress progress-thin mb-2">
                            <div class="progress-bar bg-success" style="width: {{ $progreso->Porcentaje ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ $progreso->Temas_completados ?? 0 }}/{{ $totalTemasCurso }} temas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-primary">
                    <i class="fas fa-book fa-2x mb-2"></i>
                    <h4>{{ $progreso->Temas_completados ?? 0 }}</h4>
                    <p class="mb-0">Temas Completados</p>
                    <small>{{ $totalTemasCurso }} totales</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-success">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4>{{ $evaluacionesAprobadas }}</h4>
                    <p class="mb-0">Evaluaciones Aprobadas</p>
                    <small>{{ $totalEvaluacionesCurso }} totales</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-warning">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h4>{{ $puntos->Total_puntos_actual ?? 0 }}</h4>
                    <p class="mb-0">Puntos Actuales</p>
                    <small>{{ $puntos->Total_puntos_acumulados ?? 0 }} acumulados</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-info">
                    <i class="fas fa-trophy fa-2x mb-2"></i>
                    <h4>#{{ $ranking->Posicion ?? 'N/A' }}</h4>
                    <p class="mb-0">Ranking Mensual</p>
                    <small>{{ $ranking->Total_puntos_acumulados ?? 0 }} puntos</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Temas Completados -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Temas Completados</h5>
                        <span class="badge bg-light text-primary">
                            {{ $temasCompletados->count() }}/{{ $totalTemasCurso }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($temasCompletados->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($temasCompletados as $tema)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $tema->tema_nombre }}</h6>
                                            <p class="mb-1 small text-muted">{{ $tema->tema_descripcion }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-folder me-1"></i>{{ $tema->modulo_nombre }}
                                            </small>
                                            <br>
                                            <small class="text-success">
                                                <i class="fas fa-calendar me-1"></i>
                                                Completado: {{ \Carbon\Carbon::parse($tema->Fecha_completado)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success badge-custom">
                                                {{ $tema->Porcentaje }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">El estudiante no ha completado temas aún.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Evaluaciones Completadas -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Evaluaciones Completadas</h5>
                        <span class="badge bg-light text-success">
                            {{ $evaluacionesCompletadas->count() }}/{{ $totalEvaluacionesCurso }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($evaluacionesCompletadas->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($evaluacionesCompletadas as $evaluacion)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $evaluacion->Tipo }}</h6>
                                            <p class="mb-1 small text-muted">
                                                <i class="fas fa-folder me-1"></i>{{ $evaluacion->modulo_nombre }}
                                            </p>
                                            <small class="text-muted">
                                                Máximo: {{ $evaluacion->Puntaje_maximo }} puntos
                                            </small>
                                            <br>
                                            <small class="text-info">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($evaluacion->Fecha_completado)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge {{ $evaluacion->Aprobado ? 'bg-success' : 'bg-danger' }} badge-custom mb-1">
                                                {{ $evaluacion->Puntaje_obtenido }} pts
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $evaluacion->Porcentaje }}%
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">El estudiante no ha completado evaluaciones aún.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Entregas del Estudiante -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Entregas del Estudiante</h5>
                        <span class="badge bg-light text-warning">
                            {{ $entregas->count() }} entregas
                        </span>
                    </div>
                    <div class="card-body">
                        @if($entregas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Evaluación</th>
                                            <th>Módulo</th>
                                            <th>Fecha Entrega</th>
                                            <th>Estado</th>
                                            <th>Puntos</th>
                                            <th>Comentario</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($entregas as $entrega)
                                        <tr>
                                            <td>
                                                <strong>{{ $entrega->evaluacion_tipo }}</strong>
                                                @if($entrega->Descripcion)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($entrega->Descripcion, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $entrega->modulo_nombre }}</td>
                                            <td>
                                                <small>{{ \Carbon\Carbon::parse($entrega->Fecha_entrega)->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($entrega->Estado == 'pendiente')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock me-1"></i>Pendiente
                                                    </span>
                                                @elseif($entrega->Estado == 'calificado')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Calificado
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $entrega->Estado }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="{{ $entrega->Puntos_asignados ? 'text-success' : 'text-muted' }}">
                                                    {{ $entrega->Puntos_asignados ?? 0 }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($entrega->Comentario_docente)
                                                <small class="text-muted">{{ Str::limit($entrega->Comentario_docente, 30) }}</small>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($entrega->Estado == 'pendiente')
                                                <a href="{{ route('docente.entrega.calificar', $entrega->Id_entrega) }}" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-check me-1"></i>Calificar
                                                </a>
                                                @else
                                                <button class="btn btn-info btn-sm" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Ver detalles de la entrega">
                                                    <i class="fas fa-eye me-1"></i>Ver
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                <p class="text-muted">El estudiante no ha realizado entregas aún.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>