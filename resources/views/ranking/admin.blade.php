<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking General - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .ranking-header {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            color: white;
            border-radius: 15px;
        }
        .ranking-item {
            transition: all 0.3s ease;
        }
        .ranking-item:hover {
            background-color: #f8f9fa;
        }
        .top-1 { background-color: #fff9e6; }
        .top-2 { background-color: #f8f9fa; }
        .top-3 { background-color: #fef4e8; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/admin/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav me-auto">
                <a class="nav-link text-white" href="/admin/dashboard">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link text-white" href="/ranking/admin">
                    <i class="fas fa-trophy me-1"></i>Ranking General
                </a>
                <a class="nav-link text-white" href="{{ route('admin.cursos') }}">
                    <i class="fas fa-chalkboard me-1"></i>Gestión Cursos
                </a>
                <a class="nav-link text-white" href="{{ route('admin.usuarios') }}">
                    <i class="fas fa-users me-1"></i>Gestión Usuarios
                </a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="/admin/dashboard">
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
                                <i class="fas fa-trophy me-3"></i>Ranking General del Sistema
                            </h1>
                            <p class="lead mb-0">Vista Administrador - Período: {{ $periodo_actual }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-white rounded p-3 text-dark">
                                <small class="text-muted">Total en Sistema</small>
                                <div class="fw-bold fs-1 text-danger">{{ $estadisticas_sistema['total_estudiantes'] ?? 0 }}</div>
                                <small class="text-muted">estudiantes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del Sistema -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticas_sistema['total_estudiantes'] ?? 0 }}</h4>
                        <small>Estudiantes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticas_sistema['total_docentes'] ?? 0 }}</h4>
                        <small>Docentes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticas_sistema['total_cursos'] ?? 0 }}</h4>
                        <small>Cursos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h4>{{ number_format($estadisticas_sistema['total_puntos_distribuidos'] ?? 0) }}</h4>
                        <small>Puntos Distribuidos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h4>{{ $estadisticas_sistema['total_canjes'] ?? 0 }}</h4>
                        <small>Canjes Realizados</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Completo -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">
                    <i class="fas fa-list-ol me-2"></i>Ranking General de Estudiantes
                    <small class="float-end">Período: {{ $periodo_actual }}</small>
                </h4>
            </div>
            <div class="card-body">
                @if($ranking_completo->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Estudiante</th>
                                    <th>Email</th>
                                    <th class="text-center">Puntos Acumulados</th>
                                    <th class="text-center">Puntos Actuales</th>
                                    <th class="text-center">Canjes Realizados</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ranking_completo as $index => $estudiante)
                                <tr class="ranking-item top-{{ $index < 3 ? $index + 1 : '' }}">
                                    <td>
                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>
                                            {{ $estudiante->usuario->Nombre ?? 'Estudiante' }} 
                                            {{ $estudiante->usuario->Apellido ?? '' }}
                                        </strong>
                                    </td>
                                    <td>{{ $estudiante->usuario->Email ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary">
                                            {{ number_format($estudiante->Total_puntos_acumulados) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">
                                            {{ number_format($estudiante->Total_puntos_actual) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $canjes_count = 0;
                                            if (isset($estudiante->usuario->canjes)) {
                                                $canjes_count = $estudiante->usuario->canjes->count();
                                            } elseif (isset($estudiante->canjes_count)) {
                                                $canjes_count = $estudiante->canjes_count;
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $canjes_count > 0 ? 'info' : 'secondary' }}">
                                            {{ $canjes_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ isset($estudiante->usuario->Estado) && $estudiante->usuario->Estado ? 'success' : 'danger' }}">
                                            {{ isset($estudiante->usuario->Estado) && $estudiante->usuario->Estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay datos de ranking disponibles.</p>
                        <p class="text-muted small">Los estudiantes aparecerán aquí cuando comiencen a ganar puntos.</p>
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h5>¿Por qué no hay datos?</h5>
                                <p class="mb-2">El ranking se genera automáticamente cuando:</p>
                                <ul class="text-start">
                                    <li>Los estudiantes completan cursos</li>
                                    <li>Aprueban evaluaciones</li>
                                    <li>Realizan actividades</li>
                                    <li>Ganan puntos a través del sistema</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reportes y Acciones -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Exportar Datos</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                            </button>
                            <button class="btn btn-outline-success">
                                <i class="fas fa-file-pdf me-2"></i>Generar Reporte PDF
                            </button>
                            <button class="btn btn-outline-warning">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas Detalladas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Gestión del Ranking</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-sync me-2"></i>Reiniciar Ranking Mensual
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fas fa-history me-2"></i>Ver Rankings Anteriores
                            </button>
                            <button class="btn btn-outline-dark">
                                <i class="fas fa-sliders-h me-2"></i>Configurar Parámetros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Sistema de Ranking</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>¿Cómo funciona?</h6>
                                <ul class="small">
                                    <li>Los puntos se asignan automáticamente</li>
                                    <li>El ranking se actualiza en tiempo real</li>
                                    <li>Se reinicia mensualmente</li>
                                    <li>Basado en puntos acumulados</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Puntos por Actividad</h6>
                                <ul class="small">
                                    <li>Completar tema: 10 puntos</li>
                                    <li>Aprobar evaluación: 20 puntos</li>
                                    <li>Completar módulo: 50 puntos</li>
                                    <li>Terminar curso: 200 puntos</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6>Premios Mensuales</h6>
                                <ul class="small">
                                    <li>Top 1: Beca completa</li>
                                    <li>Top 3: 50% descuento</li>
                                    <li>Top 10: 25% descuento</li>
                                    <li>Top 25: 10% descuento</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>