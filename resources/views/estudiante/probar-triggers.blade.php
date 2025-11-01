<!-- resources/views/estudiante/probar-triggers.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Probar Triggers - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .trigger-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            border: none;
        }
        .puntos-realtime {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffc107;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .btn-trigger {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
        }
        .log-item {
            border-left: 4px solid #28a745;
            padding-left: 15px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar (usa el mismo de tu dashboard) -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/estudiante/dashboard">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-flask me-2"></i>Laboratorio de Triggers - Sistema de Puntos Automático
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado Actual -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="trigger-card">
                                    <h3 class="puntos-realtime" id="puntos-actuales">
                                        {{ $gestion_puntos->Total_puntos_actual ?? 0 }}
                                    </h3>
                                    <small>Puntos Actuales</small>
                                    <div class="mt-2">
                                        <i class="fas fa-coins fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="trigger-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                    <h3 class="puntos-realtime" id="puntos-acumulados">
                                        {{ $gestion_puntos->Total_puntos_acumulados ?? 0 }}
                                    </h3>
                                    <small>Total Acumulado</small>
                                    <div class="mt-2">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="trigger-card" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                                    <h3 class="puntos-realtime" id="ranking-posicion">
                                        @if($gestion_puntos && $gestion_puntos->ranking)
                                            #{{ $gestion_puntos->ranking->Posicion ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </h3>
                                    <small>Ranking Actual</small>
                                    <div class="mt-2">
                                        <i class="fas fa-trophy fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trigger 1: Aprobar Evaluación -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>Trigger 1: Puntos por Aprobar Evaluación
                                    <small class="float-end">+20 puntos automáticos</small>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Al aprobar una evaluación, el trigger automáticamente asigna 20 puntos.</p>
                                
                                @if($evaluaciones_pendientes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Evaluación</th>
                                                <th>Curso</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evaluaciones_pendientes as $evaluacion)
                                            <tr>
                                                <td>{{ $evaluacion->evaluacion->Tipo ?? 'Evaluación' }}</td>
                                                <td>{{ $evaluacion->evaluacion->tema->modulo->curso->Titulo ?? 'Curso' }}</td>
                                                <td>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-success btn-aprobar-evaluacion" 
                                                            data-id="{{ $evaluacion->Id_progreso_evaluacion }}">
                                                        <i class="fas fa-check me-1"></i>Probar Trigger
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay evaluaciones pendientes para probar.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Trigger 2: Completar Curso -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Trigger 2: Puntos por Completar Curso
                                    <small class="float-end">+200 puntos automáticos</small>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Al completar un curso al 100%, el trigger automáticamente asigna 200 puntos.</p>
                                
                                @if($cursos_en_progreso->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Curso</th>
                                                <th>Progreso Actual</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cursos_en_progreso as $progreso)
                                            <tr>
                                                <td>{{ $progreso->curso->Titulo ?? 'Curso' }}</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" style="width: {{ $progreso->Porcentaje }}%">
                                                            {{ $progreso->Porcentaje }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-completar-curso" 
                                                            data-id="{{ $progreso->Id_progreso }}">
                                                        <i class="fas fa-flag-checkered me-1"></i>Probar Trigger
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay cursos en progreso para probar.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Log de Actividades -->
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Log de Actividades en Tiempo Real
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="log-actividades">
                                    <p class="text-muted text-center">Aquí se mostrarán los resultados de los triggers...</p>
                                </div>
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="verificarEstadoTriggers()">
                                        <i class="fas fa-sync-alt me-1"></i>Verificar Estado Actual
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function agregarLog(mensaje, tipo = 'success') {
            const timestamp = new Date().toLocaleTimeString();
            const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            
            $('#log-actividades').prepend(`
                <div class="alert ${alertClass} alert-dismissible fade show log-item" role="alert">
                    <i class="fas ${icon} me-2"></i>
                    ${mensaje}
                    <small class="text-muted ms-2">${timestamp}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
        }

        function actualizarPuntos() {
            $.get('/estudiante/obtenerPuntosActuales', function(data) {
                $('#puntos-actuales').text(data.puntos_actual);
                $('#puntos-acumulados').text(data.puntos_acumulados);
                if (data.ranking_posicion) {
                    $('#ranking-posicion').text('#' + data.ranking_posicion);
                }
            });
        }

        function verificarEstadoTriggers() {
            $.get('/estudiante/verificar-triggers', function(response) {
                if (response.success) {
                    agregarLog('✅ Estado verificado - Triggers activos y funcionando', 'success');
                }
            });
        }

        $(document).ready(function() {
            // Trigger 1: Aprobar evaluación
            $('.btn-aprobar-evaluacion').click(function() {
                const evaluacionId = $(this).data('id');
                const button = $(this);
                
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Procesando...');
                
                $.post('/estudiante/simular-aprobacion', {
                    _token: '{{ csrf_token() }}',
                    evaluacion_id: evaluacionId
                }, function(response) {
                    if (response.success) {
                        agregarLog(response.message, 'success');
                        // Actualizar puntos después de 3 segundos
                        setTimeout(actualizarPuntos, 3000);
                        
                        button.closest('tr').find('.badge')
                            .removeClass('bg-warning')
                            .addClass('bg-success')
                            .text('Aprobada');
                            
                        button.html('<i class="fas fa-check me-1"></i>Probado').removeClass('btn-success').addClass('btn-secondary');
                    } else {
                        agregarLog('❌ ' + response.message, 'danger');
                        button.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Probar Trigger');
                    }
                }).fail(function(xhr) {
                    agregarLog('❌ Error: ' + (xhr.responseJSON?.message || 'Error desconocido'), 'danger');
                    button.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Probar Trigger');
                });
            });

            // Trigger 2: Completar curso
            $('.btn-completar-curso').click(function() {
                const cursoId = $(this).data('id');
                const button = $(this);
                
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Procesando...');
                
                $.post('/estudiante/simular-completar-curso', {
                    _token: '{{ csrf_token() }}',
                    curso_id: cursoId
                }, function(response) {
                    if (response.success) {
                        agregarLog(response.message, 'success');
                        // Actualizar puntos después de 3 segundos
                        setTimeout(actualizarPuntos, 3000);
                        
                        button.closest('tr').find('.progress-bar')
                            .css('width', '100%')
                            .text('100%');
                            
                        button.html('<i class="fas fa-check me-1"></i>Probado').removeClass('btn-info').addClass('btn-secondary');
                    } else {
                        agregarLog('❌ ' + response.message, 'danger');
                        button.prop('disabled', false).html('<i class="fas fa-flag-checkered me-1"></i>Probar Trigger');
                    }
                }).fail(function(xhr) {
                    agregarLog('❌ Error: ' + (xhr.responseJSON?.message || 'Error desconocido'), 'danger');
                    button.prop('disabled', false).html('<i class="fas fa-flag-checkered me-1"></i>Probar Trigger');
                });
            });

            // Actualizar puntos cada 10 segundos
            setInterval(actualizarPuntos, 10000);
        });
    </script>
</body>
</html>