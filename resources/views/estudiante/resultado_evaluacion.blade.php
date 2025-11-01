<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - {{ $evaluacion->Tipo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Card de resultado -->
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center {{ $progreso->Aprobado ? 'bg-success' : 'bg-danger' }}">
                        <h3 class="mb-0">
                            <i class="fas fa-{{ $progreso->Aprobado ? 'check-circle' : 'times-circle' }} me-2"></i>
                            {{ $progreso->Aprobado ? '¡Evaluación Aprobada!' : 'Evaluación No Aprobada' }}
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <!-- Puntaje -->
                        <div class="mb-4">
                            <h1 class="display-4 fw-bold {{ $progreso->Aprobado ? 'text-success' : 'text-danger' }}">
                                {{ $progreso->Puntaje_obtenido }}/{{ $evaluacion->Puntaje_maximo }}
                            </h1>
                            <p class="fs-5 text-muted">Puntaje Obtenido</p>
                        </div>

                        <!-- Porcentaje -->
                        <div class="mb-4">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $progreso->Aprobado ? 'bg-success' : 'bg-danger' }}" 
                                     style="width: {{ $progreso->Porcentaje }}%">
                                    {{ number_format($progreso->Porcentaje, 1) }}%
                                </div>
                            </div>
                            <small class="text-muted">Porcentaje de acierto</small>
                        </div>

                        <!-- Información adicional -->
                        <div class="row text-center mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Estado</h6>
                                        <span class="badge bg-{{ $progreso->Aprobado ? 'success' : 'danger' }} fs-6">
                                            {{ $progreso->Aprobado ? 'Aprobado' : 'No Aprobado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Fecha</h6>
                                        <p class="mb-0 small">
                                            {{ \Carbon\Carbon::parse($progreso->Fecha_completado)->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Puntos ganados -->
                        @if($progreso->Aprobado)
                        <div class="alert alert-success">
                            <i class="fas fa-coins me-2"></i>
                            <strong>¡Felicidades!</strong> Ganaste <strong>20 puntos</strong> por aprobar esta evaluación.
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>No te rindas.</strong> Puedes intentar esta evaluación nuevamente después de repasar el material.
                        </div>
                        @endif

                        <!-- Botones de acción -->
                        <div class="d-grid gap-2 d-md-flex justify-content-center">
                            <a href="{{ route('estudiante.curso.ver', $evaluacion->Id_curso ?? '') }}" 
                               class="btn btn-primary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Curso
                            </a>
                            <a href="/estudiante/dashboard" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-1"></i>Ir al Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información de la evaluación -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Evaluación</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Evaluación:</strong> {{ $evaluacion->Tipo }}<br>
                                <strong>Módulo:</strong> {{ $evaluacion->modulo_nombre }}<br>
                                <strong>Curso:</strong> {{ $evaluacion->curso_titulo }}
                            </div>
                            <div class="col-md-6">
                                <strong>Puntaje máximo:</strong> {{ $evaluacion->Puntaje_maximo }} puntos<br>
                                <strong>Porcentaje mínimo:</strong> 60%<br>
                                <strong>Tu porcentaje:</strong> {{ number_format($progreso->Porcentaje, 1) }}%
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