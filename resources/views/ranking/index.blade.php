<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .ranking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .ranking-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .ranking-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa;
        }
        .top-1 { border-left-color: #ffd700; background-color: #fff9e6; }
        .top-2 { border-left-color: #c0c0c0; background-color: #f8f9fa; }
        .top-3 { border-left-color: #cd7f32; background-color: #fef4e8; }
        .medal { font-size: 1.5rem; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="/estudiante/dashboard">
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
                            <p class="lead mb-0">Período: {{ $estadisticas['periodo_actual'] }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($mi_posicion)
                            <div class="bg-white rounded p-3 text-dark">
                                <small class="text-muted">Tu Posición</small>
                                <div class="fw-bold fs-1 text-warning">#{{ $mi_posicion }}</div>
                                <small class="text-muted">de {{ $estadisticas['total_participantes'] }} estudiantes</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3>{{ $estadisticas['total_participantes'] }}</h3>
                        <p class="mb-0">Participantes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3>{{ number_format($estadisticas['puntos_promedio']) }}</h3>
                        <p class="mb-0">Puntos Promedio</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h3>{{ number_format($estadisticas['puntos_maximos']) }}</h3>
                        <p class="mb-0">Puntos Máximos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3>{{ $top_10->count() }}</h3>
                        <p class="mb-0">Top 10</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top 10 -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-crown me-2"></i>Top 10 Estudiantes
                            <small class="float-end">Actualizado: {{ now()->format('d/m/Y H:i') }}</small>
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($top_10->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($top_10 as $index => $estudiante)
                                <div class="list-group-item ranking-item top-{{ $index + 1 }}">
                                    <div class="row align-items-center">
                                        <div class="col-1 text-center">
                                            @if($index == 0)
                                                <span class="medal text-warning">🥇</span>
                                            @elseif($index == 1)
                                                <span class="medal text-secondary">🥈</span>
                                            @elseif($index == 2)
                                                <span class="medal text-danger">🥉</span>
                                            @else
                                                <span class="fw-bold text-muted">#{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-1">{{ $estudiante->usuario->Nombre ?? 'Estudiante' }} {{ $estudiante->usuario->Apellido ?? '' }}</h6>
                                            <small class="text-muted">{{ $estudiante->usuario->Email ?? '' }}</small>
                                        </div>
                                        <div class="col-3 text-center">
                                            <span class="badge bg-primary fs-6">{{ number_format($estudiante->Total_puntos_acumulados) }} pts</span>
                                        </div>
                                        <div class="col-2 text-end">
                                            <small class="text-muted">Nivel {{ floor($estudiante->Total_puntos_acumulados / 100) + 1 }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay datos de ranking disponibles.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ranking Completo -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-list-ol me-2"></i>Ranking Completo
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($ranking_general->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Posición</th>
                                            <th>Estudiante</th>
                                            <th class="text-center">Puntos Acumulados</th>
                                            <th class="text-center">Puntos Actuales</th>
                                            <th class="text-center">Nivel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ranking_general as $index => $estudiante)
                                        <tr class="{{ $estudiante->Id_usuario == $usuario->Id_usuario ? 'table-success' : '' }}">
                                            <td>
                                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $estudiante->usuario->Nombre ?? 'Estudiante' }} {{ $estudiante->usuario->Apellido ?? '' }}</strong>
                                                @if($estudiante->Id_usuario == $usuario->Id_usuario)
                                                    <span class="badge bg-success ms-2">Tú</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-primary">{{ number_format($estudiante->Total_puntos_acumulados) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-success">{{ number_format($estudiante->Total_puntos_actual) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">Nivel {{ floor($estudiante->Total_puntos_acumulados / 100) + 1 }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información Lateral -->
            <div class="col-lg-4">
                <!-- Cómo subir en el ranking -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>¿Cómo subir en el ranking?</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Completar temas: <strong>10 puntos</strong></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Aprobar evaluaciones: <strong>20 puntos</strong></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Completar módulos: <strong>50 puntos</strong></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Terminar cursos: <strong>200 puntos</strong></span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Ganar insignias: <strong>+50 puntos</strong></span>
                        </div>
                        <hr>
                        <small class="text-muted">
                            El ranking se actualiza automáticamente cada vez que ganas puntos.
                            Se reinicia mensualmente.
                        </small>
                    </div>
                </div>

                <!-- Próximos premios -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-gift me-2"></i>Próximos Premios</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-warning me-2">Top 1</span>
                            <span>Beca completa + Insignia Oro</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-secondary me-2">Top 3</span>
                            <span>50% descuento + Insignia Plata</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-danger me-2">Top 10</span>
                            <span>25% descuento + Insignia Bronce</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-primary me-2">Top 25</span>
                            <span>10% descuento en próximo curso</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>