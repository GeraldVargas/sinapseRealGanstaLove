<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes - {{ $curso->Titulo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/admin/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin/dashboard">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Estudiantes del Curso: {{ $curso->Titulo }}</h1>
                        <p class="text-muted">{{ $estudiantes->count() }} estudiantes inscritos</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6">{{ $curso->Duracion }}h</span>
                        <span class="badge bg-success fs-6 ms-2">${{ number_format($curso->Costo, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-users me-2"></i>Lista de Estudiantes Inscritos</h4>
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
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                            @php
                                // Obtener la inscripción del estudiante en este curso
                                $inscripcion = $estudiante->inscripciones->where('Id_curso', $curso->Id_curso)->first();
                                // Obtener progreso del estudiante en este curso
                                $progreso = $estudiante->progresos->where('Id_curso', $curso->Id_curso)->first();
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
                                    {{ $inscripcion->Fecha_inscripcion ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($estudiante->Estado)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 100px;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $porcentaje }}%</small>
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                {{ $progreso->Modulos_Com ?? 0 }}/{{ $curso->modulos->count() }} módulos
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detalle
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-chart-line"></i> Progreso
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No hay estudiantes inscritos</h4>
                    <p class="text-muted">No hay estudiantes inscritos en este curso.</p>
                    <a href="/admin/dashboard" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Estadísticas del Curso -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3>{{ $estudiantes->count() }}</h3>
                        <p class="mb-0">Total Estudiantes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3>{{ $estudiantes->where('Estado', 1)->count() }}</h3>
                        <p class="mb-0">Estudiantes Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        @php
                            $promedioProgreso = $estudiantes->count() > 0 ? 
                                $estudiantes->reduce(function($carry, $estudiante) use ($curso) {
                                    $progreso = $estudiante->progresos->where('Id_curso', $curso->Id_curso)->first();
                                    return $carry + ($progreso ? $progreso->Porcentaje : 0);
                                }, 0) / $estudiantes->count() : 0;
                        @endphp
                        <h3>{{ round($promedioProgreso) }}%</h3>
                        <p class="mb-0">Progreso Promedio</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3>{{ $curso->modulos->count() }}</h3>
                        <p class="mb-0">Módulos del Curso</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>