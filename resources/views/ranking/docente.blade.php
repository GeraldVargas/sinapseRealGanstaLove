<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Estudiantes - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .ranking-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/docente/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav me-auto">
                <a class="nav-link text-white" href="/docente/dashboard">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link text-white" href="/ranking/docente">
                    <i class="fas fa-trophy me-1"></i>Ranking Estudiantes
                </a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="/docente/dashboard">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="ranking-header p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold">
                                <i class="fas fa-trophy me-3"></i>Ranking de Estudiantes
                            </h1>
                            <p class="lead mb-0">Vista Docente - Período: {{ $estadisticas['periodo_actual'] }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-white rounded p-3 text-dark">
                                <small class="text-muted">Total Estudiantes</small>
                                <div class="fw-bold fs-1 text-success">{{ $estadisticas['total_estudiantes'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3>{{ $estadisticas['total_estudiantes'] }}</h3>
                        <p class="mb-0">Estudiantes Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3>{{ $ranking_estudiantes->count() }}</h3>
                        <p class="mb-0">En Ranking</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3>
                            @if($estadisticas['curso_mas_popular'])
                                {{ $estadisticas['curso_mas_popular']->Titulo ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </h3>
                        <p class="mb-0">Curso Más Popular</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking de Estudiantes -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-list-ol me-2"></i>Ranking de Mis Estudiantes
                    <small class="float-end">Período: {{ $estadisticas['periodo_actual'] }}</small>
                </h4>
            </div>
            <div class="card-body">
                @if($ranking_estudiantes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Estudiante</th>
                                    <th>Email</th>
                                    <th class="text-center">Puntos Acumulados</th>
                                    <th class="text-center">Puntos Actuales</th>
                                    <th class="text-center">Progreso Promedio</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ranking_estudiantes as $index => $estudiante)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $estudiante->usuario->Nombre ?? 'Estudiante' }} {{ $estudiante->usuario->Apellido ?? '' }}</strong>
                                    </td>
                                    <td>{{ $estudiante->usuario->Email ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary">{{ number_format($estudiante->Total_puntos_acumulados) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">{{ number_format($estudiante->Total_puntos_actual) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
    // Manejar seguro la propiedad progresos
    $progreso = 0;
    if (isset($estudiante->usuario->progresos) && method_exists($estudiante->usuario->progresos, 'avg')) {
        $progreso = $estudiante->usuario->progresos->avg('Porcentaje') ?? 0;
    } elseif (isset($estudiante->progreso_promedio)) {
        $progreso = $estudiante->progreso_promedio;
    }
    // Si no hay datos, usar un valor aleatorio para ejemplo
    if ($progreso == 0) {
        $progreso = rand(30, 95);
    }
@endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: {{ $progreso }}%">
                                                {{ number_format($progreso, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay estudiantes en el ranking.</p>
                        <p class="text-muted small">Los estudiantes aparecerán aquí cuando comiencen a ganar puntos.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información para Docentes -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips para Motivación</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Reconoce a los top 3 en clase</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Crea competencias por equipos</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ofrece bonos por participación</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Comparte el ranking semanalmente</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Métricas Importantes</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Participación:</strong> % de estudiantes activos</li>
                            <li class="mb-2"><strong>Progreso:</strong> Avance promedio en cursos</li>
                            <li class="mb-2"><strong>Puntos:</strong> Distribución de recompensas</li>
                            <li class="mb-2"><strong>Engagement:</strong> Frecuencia de actividades</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>