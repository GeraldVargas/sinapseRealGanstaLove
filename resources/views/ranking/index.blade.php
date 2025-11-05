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
            padding: 2rem 0;
            margin-bottom: 2rem;
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
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .auto-sync-badge {
            font-size: 0.7rem;
            background: rgba(255,255,255,0.2);
        }
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
        <!-- Header del Ranking -->
        <div class="ranking-header">
            <div class="container text-center">
                <h1 class="display-4 fw-bold">
                    <i class="fas fa-trophy me-3"></i>Ranking de Estudiantes
                </h1>
                <p class="lead mb-3">Período: {{ $periodo }}</p>
                <span class="badge auto-sync-badge">
                    <i class="fas fa-sync-alt me-1"></i>Sincronización automática
                </span>
                
                <div class="row mt-4">
                    <div class="col-md-8 offset-md-2">
                        <div class="bg-white rounded p-3 text-dark shadow">
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted">Periodo Actual</small>
                                    <h4 class="text-primary mb-0">{{ $periodo }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Total de Participantes</small>
                                    <h4 class="text-success mb-0">{{ $total_participantes }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Actualizado</small>
                                    <h5 class="text-info mb-0">{{ now()->format('d/m H:i') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
        <div class="container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @if(empty($ranking_completo))
        <!-- Ranking Vacío -->
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h4>El ranking aún no está disponible</h4>
                        <p class="mb-0">No hay estudiantes en el ranking actualmente.</p>
                        <small class="text-muted">Los estudiantes aparecerán cuando obtengan puntos.</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Contenido cuando hay ranking -->
        
        <!-- Mi Posición -->
        @if($miPosicion)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-user me-2"></i>Tu Posición en el Ranking
                        </h5>
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <h2 class="display-3 text-warning">#{{ $miPosicion->Posicion }}</h2>
                                <small class="text-muted">de {{ $total_participantes }} participantes</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="text-success">{{ number_format($miPosicion->Total_puntos_acumulados) }}</h4>
                                <small class="text-muted">puntos acumulados</small>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        {{ substr($miPosicion->Nombre, 0, 1) }}{{ substr($miPosicion->Apellido, 0, 1) }}
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $miPosicion->Nombre }} {{ $miPosicion->Apellido }}</h5>
                                        <small class="text-muted">{{ $miPosicion->Email }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3>{{ $estadisticas['total_participantes'] }}</h3>
                        <p class="mb-0">Participantes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3>{{ number_format($estadisticas['puntos_promedio']) }}</h3>
                        <p class="mb-0">Puntos Promedio</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h3>{{ number_format($estadisticas['puntos_maximos']) }}</h3>
                        <p class="mb-0">Puntos Máximos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3>{{ count($top_10) }}</h3>
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
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($top_10 as $participante)
                            <div class="list-group-item ranking-item top-{{ $participante->Posicion }}">
                                <div class="row align-items-center">
                                    <div class="col-1 text-center">
                                        @if($participante->Posicion == 1)
                                            <span class="medal text-warning">🥇</span>
                                        @elseif($participante->Posicion == 2)
                                            <span class="medal text-secondary">🥈</span>
                                        @elseif($participante->Posicion == 3)
                                            <span class="medal text-danger">🥉</span>
                                        @else
                                            <span class="fw-bold text-muted">#{{ $participante->Posicion }}</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                {{ substr($participante->Nombre, 0, 1) }}{{ substr($participante->Apellido, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $participante->Nombre }} {{ $participante->Apellido }}</h6>
                                                <small class="text-muted">{{ $participante->Email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 text-center">
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($participante->Total_puntos_acumulados) }} pts
                                        </span>
                                    </div>
                                    <div class="col-2 text-end">
                                        @php
                                            $nivel = floor($participante->Total_puntos_acumulados / 100) + 1;
                                        @endphp
                                        <small class="text-muted">Nivel {{ $nivel }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>Estudiante</th>
                                        <th class="text-center">Puntos</th>
                                        <th class="text-center">Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking_completo as $participante)
                                    <tr class="{{ $participante->Id_usuario == $usuario->Id_usuario ? 'table-success' : '' }}">
                                        <td>
                                            <span class="badge bg-{{ $participante->Posicion <= 3 ? 'warning' : 'secondary' }}">
                                                #{{ $participante->Posicion }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    {{ substr($participante->Nombre, 0, 1) }}{{ substr($participante->Apellido, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $participante->Nombre }} {{ $participante->Apellido }}</strong>
                                                    @if($participante->Id_usuario == $usuario->Id_usuario)
                                                        <span class="badge bg-success ms-2">Tú</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ $participante->Email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-primary">
                                                {{ number_format($participante->Total_puntos_acumulados) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $nivel = floor($participante->Total_puntos_acumulados / 100) + 1;
                                                $color = 'bg-secondary';
                                                if ($nivel >= 10) $color = 'bg-danger';
                                                elseif ($nivel >= 5) $color = 'bg-warning';
                                                elseif ($nivel >= 3) $color = 'bg-info';
                                            @endphp
                                            <span class="badge {{ $color }}">Nivel {{ $nivel }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>