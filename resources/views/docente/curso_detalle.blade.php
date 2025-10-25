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
                </ul>

                <div class="tab-content mt-3" id="cursoTabsContent">
                    <!-- Tab Estudiantes -->
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
                                                $progreso = $estudiante->progresos->where('id_curso', $curso->Id_curso)->first();
                                                $porcentaje = $progreso ? $progreso->Porcentaje : 0;
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
                                                <td>
                                                    {{ $estudiante->inscripciones->where('id_curso', $curso->Id_curso)->first()->Fecha_inscripcion ?? 'N/A' }}
                                                </td>
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
                                                                {{ $progreso->Modulos_Com ?? 0 }}/{{ $modulos->count() }} módulos
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
                                                        <button class="btn btn-outline-warning" title="Editar progreso">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
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

                    <!-- Los otros tabs (módulos y evaluaciones) se mantienen igual -->
                    <div class="tab-pane fade" id="modulos" role="tabpanel">
                        <!-- Contenido de módulos (igual que antes) -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Módulos del Curso</h5>
                            </div>
                            <div class="card-body">
                                @if($modulos->count() > 0)
                                <div class="list-group">
                                    @foreach($modulos as $modulo)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $modulo->Nombre }}</h6>
                                                <p class="mb-1 text-muted small">{{ $modulo->Descripcion }}</p>
                                                <small class="text-muted">
                                                    {{ $modulo->temas->count() }} temas
                                                </small>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-plus"></i> Agregar Tema
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay módulos creados para este curso.</p>
                                    <button class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Crear Primer Módulo
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="evaluaciones" role="tabpanel">
                        <!-- Contenido de evaluaciones (igual que antes) -->
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
                                                <td>{{ $evaluacion->tema->modulo->Nombre ?? 'N/A' }}</td>
                                                <td>{{ $evaluacion->Puntaje_maximo }}</td>
                                                <td>{{ $evaluacion->Fecha_inicio }}</td>
                                                <td>{{ $evaluacion->fecha_fin }}</td>
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

    <!-- Modal Evaluación (se mantiene igual) -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>