<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $curso->Titulo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .progress-thin {
            height: 6px;
        }
        .student-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .entregas-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
                <a class="nav-link" href="/docente/dashboard">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </nav>
    <div class="container my-4">
    <!-- Alertas de éxito/error -->
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

    <div class="container my-4">
        <!-- Header del Curso -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>{{ $curso->Titulo }}</h1>
                        <p class="text-muted">{{ $curso->Descripcion }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6">{{ $curso->Duracion }} horas</span>
                        <span class="badge bg-success fs-6 ms-2">{{ $estudiantes->count() }} Estudiantes</span>
                    </div>
                </div>

                <!-- 🆕 ALERTA DE ENTREGAS PENDIENTES -->
                @if($entregasPendientesCount > 0)
                <div class="alert alert-warning alert-dismissible fade show mt-3 entregas-badge" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tienes <strong>{{ $entregasPendientesCount }}</strong> entrega(s) pendiente(s) de calificación en este curso.
                    <a href="#entregas" class="alert-link fw-bold">Revisar ahora</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Pestañas -->
            <div class="col-12">
                <ul class="nav nav-tabs" id="cursoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="estudiantes-tab" data-bs-toggle="tab" data-bs-target="#estudiantes">
                            <i class="fas fa-users me-2"></i>Estudiantes ({{ $estudiantes->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modulos-tab" data-bs-toggle="tab" data-bs-target="#modulos">
                            <i class="fas fa-book me-2"></i>Módulos ({{ $modulos->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="evaluaciones-tab" data-bs-toggle="tab" data-bs-target="#evaluaciones">
                            <i class="fas fa-file-alt me-2"></i>Evaluaciones ({{ $evaluaciones->count() }})
                        </button>
                    </li>
                    <!-- 🆕 NUEVA PESTAÑA: ENTREGAS PENDIENTES -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="entregas-tab" data-bs-toggle="tab" data-bs-target="#entregas">
                            <i class="fas fa-file-upload me-2"></i>Entregas
                            @if($entregasPendientesCount > 0)
                            <span class="badge bg-danger ms-1">{{ $entregasPendientesCount }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="cursoTabsContent">
                    <!-- Tab Estudiantes CORREGIDO -->
                    <div class="tab-pane fade show active" id="estudiantes" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Estudiantes</h5>
                                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarEstudiante">
                                    <i class="fas fa-user-plus me-1"></i>Agregar Estudiante
                                </button>
                            </div>
                            <div class="card-body">
                                @if($estudiantes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Estudiante</th>
                                                <th>Email</th>
                                                <th>Fecha Inscripción</th>
                                                <th>Progreso</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estudiantes as $estudiante)
                                            @php
                                                $porcentaje = $estudiante->progreso ?? 0;
                                                $modulos_completados = $estudiante->Modulos_completados ?? 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="student-avatar me-3">
                                                            {{ substr($estudiante->Nombre, 0, 1) }}{{ substr($estudiante->Apellido, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $estudiante->Nombre }} {{ $estudiante->Apellido }}</strong>
                                                            <br>
                                                            <small class="text-muted">ID: {{ $estudiante->Id_usuario }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $estudiante->Email }}</td>
                                                <td>{{ $estudiante->Fecha_inscripcion ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3" style="width: 80px;">
                                                            <div class="progress progress-thin">
                                                                <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                                                            </div>
                                                            <small class="text-muted">{{ $porcentaje }}%</small>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">
                                                                {{ $modulos_completados }}/{{ $modulos->count() }} módulos
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($porcentaje >= 80)
                                                        <span class="badge bg-success">Avanzado</span>
                                                    @elseif($porcentaje >= 50)
                                                        <span class="badge bg-warning">Intermedio</span>
                                                    @else
                                                        <span class="badge bg-info">Principiante</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('docente.estudiante.detalle', ['idCurso' => $curso->Id_curso, 'idEstudiante' => $estudiante->Id_usuario]) }}" 
                                                           class="btn btn-outline-primary" title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('docente.estudiante.eliminar', ['idCurso' => $curso->Id_curso, 'idEstudiante' => $estudiante->Id_usuario]) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" 
                                                                    title="Eliminar del curso"
                                                                    onclick="return confirm('¿Estás seguro de eliminar a {{ $estudiante->Nombre }} de este curso?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay estudiantes inscritos en este curso.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarEstudiante">
                                        <i class="fas fa-user-plus me-2"></i>Agregar Primer Estudiante
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tab Módulos - VERSIÓN MEJORADA -->
                    <div class="tab-pane fade" id="modulos" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Módulos del Curso</h5>
                                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearModulo">
                                    <i class="fas fa-plus me-1"></i>Crear Módulo
                                </button>
                            </div>
                            <div class="card-body">
                                @if($modulos->count() > 0)
                                <div class="list-group">
                                    @foreach($modulos as $modulo)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $modulo->Nombre }}</h6>
                                                <p class="mb-1 text-muted small">{{ $modulo->Descripcion }}</p>
                                             <div class="d-flex gap-2 mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-file-alt me-1"></i>{{ $modulo->total_temas }} temas
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-tasks me-1"></i>{{ $modulo->total_evaluaciones }} evaluaciones
                                    </small>
                                </div>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" 
                                                        data-bs-target="#modalCrearTema" 
                                                        data-modulo-id="{{ $modulo->Id_modulo }}"
                                                        data-modulo-nombre="{{ $modulo->Nombre }}">
                                                    <i class="fas fa-plus"></i> Tema
                                                </button>
                                                <button class="btn btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-eliminar-modulo" 
                                                        data-modulo-id="{{ $modulo->Id_modulo }}"
                                                        data-modulo-nombre="{{ $modulo->Nombre }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Lista de Temas del Módulo -->
                                        @if($modulo->total_temas > 0)
                                        <div class="mt-3 ps-4 border-start border-2 border-success">
                                            <h6 class="text-success mb-2">
                                                <i class="fas fa-list me-1"></i>Temas:
                                            </h6>
                                            @foreach($modulo->temas as $tema)
                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                <div>
                                                    <span class="fw-bold">Tema {{ $tema->Orden ?? 'N/A' }}:</span> {{ $tema->Nombre }}
                                                    @if($tema->Descripcion)
                                                    <br><small class="text-muted">{{ $tema->Descripcion }}</small>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm btn-eliminar-tema"
                                                            data-tema-id="{{ $tema->Id_tema }}"
                                                            data-tema-nombre="{{ $tema->Nombre }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay módulos creados para este curso.</p>
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearModulo">
                                        <i class="fas fa-plus me-2"></i>Crear Primer Módulo
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tab Evaluaciones CORREGIDO -->
                    <div class="tab-pane fade" id="evaluaciones" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Evaluaciones del Curso</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Gestionar Evaluaciones</h6>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEvaluacion">
                                        <i class="fas fa-plus me-2"></i>Nueva Evaluación
                                    </button>
                                </div>

                                @if($evaluaciones->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Módulo</th>
                                                <th>Puntaje Máx</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evaluaciones as $evaluacion)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info">{{ $evaluacion->Tipo }}</span>
                                                </td>
                                                <td>{{ $evaluacion->modulo_nombre ?? 'N/A' }}</td>
                                                <td>{{ $evaluacion->Puntaje_maximo }}</td>
                                                <td>{{ $evaluacion->Fecha_inicio }}</td>
                                                <td>{{ $evaluacion->Fecha_fin }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay evaluaciones creadas para este curso.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 🆕 PESTAÑA: ENTREGAS PENDIENTES - CORREGIDA -->
                    <div class="tab-pane fade" id="entregas" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center 
                                        {{ $entregasPendientesCount > 0 ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-upload me-2"></i>
                                    @if($entregasPendientesCount > 0)
                                        Entregas Pendientes de Calificación
                                    @else
                                        Entregas al Corriente
                                    @endif
                                </h5>
                                @if($entregasPendientesCount > 0)
                                    <span class="badge bg-light text-danger">{{ $entregasPendientesCount }} Pendientes</span>
                                @else
                                    <span class="badge bg-light text-success"><i class="fas fa-check me-1"></i>Todo al día</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($entregasPendientesCount > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Estudiante</th>
                                                    <th>Evaluación</th>
                                                    <th>Módulo</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($entregasPendientes as $entrega)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $entrega->estudiante_nombre }} {{ $entrega->estudiante_apellido }}</strong>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $entrega->evaluacion_tipo }}</strong>
                                                        <br>
                                                        <small class="text-muted">Max: {{ $entrega->Puntaje_maximo }} pts</small>
                                                    </td>
                                                    <td>{{ $entrega->modulo_nombre }}</td>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($entrega->Fecha_entrega)->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('docente.entrega.calificar', $entrega->Id_entrega) }}" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-check-circle me-1"></i>Calificar
                                                            </a>
                                                            <button type="button" class="btn btn-info btn-sm" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#verEntregaModal{{ $entrega->Id_entrega }}">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </button>
                                                        </div>

                                                        <!-- Modal para ver entrega -->
                                                        <div class="modal fade" id="verEntregaModal{{ $entrega->Id_entrega }}" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title">
                                                                            Entrega de {{ $entrega->estudiante_nombre }} {{ $entrega->estudiante_apellido }}
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <strong>Evaluación:</strong> {{ $entrega->evaluacion_tipo }}<br>
                                                                            <strong>Módulo:</strong> {{ $entrega->modulo_nombre }}<br>
                                                                            <strong>Puntaje máximo:</strong> {{ $entrega->Puntaje_maximo }} puntos<br>
                                                                            <strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($entrega->Fecha_entrega)->format('d/m/Y H:i') }}
                                                                        </div>
                                                                        
                                                                        @if($entrega->Descripcion)
                                                                        <div class="mb-3">
                                                                            <strong>Descripción del estudiante:</strong>
                                                                            <p class="mt-1 p-2 bg-light rounded">{{ $entrega->Descripcion }}</p>
                                                                        </div>
                                                                        @endif

                                                                        @if($entrega->Archivo)
                                                                        <div class="mb-3">
                                                                            <strong>Archivo adjunto:</strong>
                                                                            <div class="mt-1">
                                                                                <a href="{{ asset('storage/entregas/' . $entrega->Archivo) }}" 
                                                                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                                                                    <i class="fas fa-download me-1"></i>Descargar Archivo
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        @else
                                                                        <div class="alert alert-info">
                                                                            <i class="fas fa-info-circle me-2"></i>
                                                                            Esta entrega no tiene archivo adjunto.
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ route('docente.entrega.calificar', $entrega->Id_entrega) }}" 
                                                                           class="btn btn-warning">
                                                                            <i class="fas fa-check-circle me-1"></i>Ir a Calificar
                                                                        </a>
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                                        <h5 class="text-success">¡No hay entregas pendientes!</h5>
                                        <p class="text-muted">Todas las entregas han sido calificadas en este curso.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Estudiante -->
    <div class="modal fade" id="modalAgregarEstudiante" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Agregar Estudiante al Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('docente.estudiante.agregar', $curso->Id_curso) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Estudiante</label>
                            <select class="form-control" name="id_estudiante" required>
                                <option value="">Seleccionar estudiante...</option>
                                @foreach($todos_estudiantes as $estudiante)
                                <option value="{{ $estudiante->Id_usuario }}">
                                    {{ $estudiante->Nombre }} {{ $estudiante->Apellido }} ({{ $estudiante->Email }})
                                </option>
                                @endforeach
                            </select>
                            @if($todos_estudiantes->count() == 0)
                            <div class="alert alert-info mt-2">
                                <small>No hay estudiantes disponibles para agregar. Todos los estudiantes ya están inscritos en este curso.</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" {{ $todos_estudiantes->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-user-plus me-2"></i>Agregar Estudiante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Evaluación ACTUALIZADO -->
    <div class="modal fade" id="modalEvaluacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Crear Nueva Evaluación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('docente.evaluacion.crear', $curso->Id_curso) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Módulo</label>
                            <select class="form-control" name="Id_modulo" required>
                                <option value="">Seleccionar módulo...</option>
                                @foreach($modulos as $modulo)
                                <option value="{{ $modulo->Id_modulo }}">{{ $modulo->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Evaluación</label>
                            <select class="form-control" name="tipo" required>
                                <option value="Quiz">Quiz</option>
                                <option value="Examen Parcial">Examen Parcial</option>
                                <option value="Examen Final">Examen Final</option>
                                <option value="Proyecto">Proyecto</option>
                                <option value="Tarea">Tarea</option>
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

    <!-- Modal Crear Módulo - VERSIÓN CORREGIDA -->
    <div class="modal fade" id="modalCrearModulo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Crear Nuevo Módulo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('docente.modulo.crear', $curso->Id_curso) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Módulo *</label>
                            <input type="text" class="form-control" name="nombre" required 
                                   placeholder="Ej: Introducción a la Programación"
                                   value="{{ old('nombre') }}">
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción *</label>
                            <textarea class="form-control" name="descripcion" rows="3" required 
                                      placeholder="Describe los objetivos y contenido de este módulo...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Crear Módulo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REEMPLAZAR el modal "Crear Tema" existente con este: -->
<!-- Modal Crear Tema - VERSIÓN MEJORADA CON CONTENIDO COMPLETO -->
<div class="modal fade" id="modalCrearTema" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Crear Nuevo Tema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearTema" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_modulo" id="inputIdModulo">
                    <div class="mb-3">
                        <label class="form-label">Módulo</label>
                        <input type="text" class="form-control" id="inputNombreModulo" readonly>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Tema *</label>
                                <input type="text" class="form-control" name="nombre" required 
                                       placeholder="Ej: Variables y Tipos de Datos">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Orden *</label>
                                <input type="number" class="form-control" name="orden" required min="1" 
                                       placeholder="1" value="1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción *</label>
                        <textarea class="form-control" name="descripcion" rows="2" required 
                                  placeholder="Breve descripción del tema..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Contenido</label>
                        <select class="form-select" id="tipo_contenido" name="tipo_contenido">
                            <option value="texto">Texto/HTML</option>
                            <option value="video">Video</option>
                            <option value="pdf">PDF</option>
                            <option value="presentacion">Presentación</option>
                        </select>
                    </div>
                    
                    <!-- Contenido HTML (visible por defecto) -->
                    <div class="mb-3" id="contenido_html_group">
                        <label class="form-label">Contenido HTML *</label>
                        <textarea class="form-control" name="contenido_html" rows="8" 
                                  placeholder="Escribe el contenido del tema. Puedes usar HTML para formato..."></textarea>
                        <small class="text-muted">Puedes usar etiquetas HTML como &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, etc.</small>
                    </div>
                    
                    <!-- URL de Video (oculto por defecto) -->
                    <div class="mb-3" id="url_video_group" style="display: none;">
                        <label class="form-label">URL del Video *</label>
                        <input type="url" class="form-control" name="url_video" 
                               placeholder="https://www.youtube.com/watch?v=... o https://vimeo.com/...">
                        <small class="text-muted">Soporta YouTube, Vimeo y otros servicios de video.</small>
                    </div>
                    
                    <!-- Archivo Adjunto (oculto por defecto) -->
                    <div class="mb-3" id="archivo_group" style="display: none;">
                        <label class="form-label">Archivo Adjunto *</label>
                        <input type="file" class="form-control" name="archivo_adjunto">
                        <small class="text-muted">Formatos permitidos: PDF, PPT, DOC, imágenes. Máx: 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Tema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de crear tema
    const modalCrearTema = document.getElementById('modalCrearTema');
    modalCrearTema.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const moduloId = button.getAttribute('data-modulo-id');
        const moduloNombre = button.getAttribute('data-modulo-nombre');
        
        document.getElementById('inputIdModulo').value = moduloId;
        document.getElementById('inputNombreModulo').value = moduloNombre;
        
        // Configurar la acción del formulario
        const form = document.getElementById('formCrearTema');
        form.action = `/docente/modulo/${moduloId}/tema/crear`;
        
        // Resetear campos al abrir modal
        form.reset();
        document.getElementById('tipo_contenido').value = 'texto';
        mostrarCamposPorTipo('texto');
        
        console.log('Form action set to:', form.action);
    });

    // Manejar cambio de tipo de contenido
    document.getElementById('tipo_contenido').addEventListener('change', function() {
        mostrarCamposPorTipo(this.value);
    });

    function mostrarCamposPorTipo(tipo) {
        // Ocultar todos los grupos primero
        document.getElementById('contenido_html_group').style.display = 'none';
        document.getElementById('url_video_group').style.display = 'none';
        document.getElementById('archivo_group').style.display = 'none';
        
        // Mostrar solo el grupo correspondiente
        switch(tipo) {
            case 'texto':
                document.getElementById('contenido_html_group').style.display = 'block';
                break;
            case 'video':
                document.getElementById('url_video_group').style.display = 'block';
                break;
            case 'pdf':
            case 'presentacion':
                document.getElementById('archivo_group').style.display = 'block';
                break;
        }
    }

    // Eliminar módulo
    document.querySelectorAll('.btn-eliminar-modulo').forEach(button => {
        button.addEventListener('click', function() {
            const moduloId = this.getAttribute('data-modulo-id');
            const moduloNombre = this.getAttribute('data-modulo-nombre');
            
            if (confirm(`¿Estás seguro de eliminar el módulo "${moduloNombre}"? Esta acción no se puede deshacer.`)) {
                fetch(`/docente/modulo/${moduloId}/eliminar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error al eliminar el módulo');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el módulo');
                });
            }
        });
    });

    // Eliminar tema
    document.querySelectorAll('.btn-eliminar-tema').forEach(button => {
        button.addEventListener('click', function() {
            const temaId = this.getAttribute('data-tema-id');
            const temaNombre = this.getAttribute('data-tema-nombre');
            
            if (confirm(`¿Estás seguro de eliminar el tema "${temaNombre}"?`)) {
                fetch(`/docente/tema/${temaId}/eliminar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error al eliminar el tema');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el tema');
                });
            }
        });
    });
});
</script>
</body>
</html>