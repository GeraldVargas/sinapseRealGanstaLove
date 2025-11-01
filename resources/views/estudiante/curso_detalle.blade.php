<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $curso->Titulo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .curso-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .modulo-card {
            border-left: 4px solid #667eea;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .modulo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .tema-item {
            border-left: 3px solid #28a745;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .tema-completado {
            border-left-color: #6c757d;
            background: #e9ecef;
            opacity: 0.8;
        }
        .tema-pendiente {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .progreso-curso {
            height: 20px;
            border-radius: 10px;
        }
        .accion-continuar {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .accion-continuar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .evaluacion-item {
            border-left: 4px solid #ffc107;
            background: #fffbf0;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .badge-completado {
            background: #28a745;
        }
        .badge-pendiente {
            background: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>
    <!-- Header del Curso -->
    <div class="curso-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/estudiante/dashboard" class="text-white"><i class="fas fa-home"></i> Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/estudiante/dashboard#mis-cursos" class="text-white">Mis Cursos</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $curso->Titulo }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-bold">{{ $curso->Titulo }}</h1>
                    <p class="lead mb-0">{{ $curso->Descripcion }}</p>
                    <div class="mt-2">
                        <span class="badge bg-light text-primary me-2">
                            <i class="fas fa-clock me-1"></i>{{ $curso->Duracion }} horas
                        </span>
                        <span class="badge bg-light text-success me-2">
                            <i class="fas fa-book me-1"></i>{{ count($modulos) }} módulos
                        </span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white rounded p-3 text-dark shadow">
                        <small class="text-muted">Progreso del Curso</small>
                        <div class="h3 text-primary mb-1">{{ $progreso->Porcentaje ?? 0 }}%</div>
                        <div class="progress progreso-curso mb-2">
                            <div class="progress-bar bg-success" style="width: {{ $progreso->Porcentaje ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ $progreso->Temas_completados ?? 0 }} temas completados
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Botón de Continuar (si hay tema pendiente) -->
        @if($proximo_tema_pendiente)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-success shadow">
                    <div class="card-body text-center py-4">
                        <h4 class="text-success mb-3">
                            <i class="fas fa-play-circle me-2"></i>Continuar Aprendizaje
                        </h4>
                        <p class="mb-3">Continúa desde donde lo dejaste:</p>
                        <h5 class="text-primary">"{{ $proximo_tema_pendiente->Nombre }}"</h5>
                        <p class="text-muted mb-3">
                            <i class="fas fa-folder me-1"></i>Módulo: {{ $proximo_tema_pendiente->modulo_nombre }}
                        </p>
                        <button class="btn accion-continuar" onclick="comenzarTema({{ $proximo_tema_pendiente->Id_tema }})">
                            <i class="fas fa-play me-2"></i>Comenzar Tema
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Módulos y Temas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-list-ol me-2"></i>Contenido del Curso
                        </h4>
                        <span class="badge bg-light text-primary">
                            {{ $progreso->Temas_completados ?? 0 }}/{{ $total_temas_curso ?? 0 }} temas completados
                        </span>
                    </div>
                    <div class="card-body">
                        @foreach($modulos as $index => $modulo)
                        <div class="card modulo-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-folder me-2"></i>Módulo {{ $index + 1 }}: {{ $modulo->Nombre }}
                                    </h5>
                                    <p class="mb-0 small text-muted mt-1">{{ $modulo->Descripcion }}</p>
                                </div>
                                <span class="badge bg-primary">
                                    {{ $modulo->temas_completados ?? 0 }}/{{ $modulo->total_temas }} temas
                                </span>
                            </div>
                            <div class="card-body">
                                @php
                                    $temas_modulo = DB::select("
                                        SELECT t.*, 
                                               COALESCE(pt.Completado, 0) as completado
                                        FROM temas t
                                        LEFT JOIN progreso_tema pt ON t.Id_tema = pt.Id_tema 
                                        WHERE t.Id_modulo = ?
                                        ORDER BY t.Orden
                                    ", [$modulo->Id_modulo]);
                                @endphp

                                @foreach($temas_modulo as $temaIndex => $tema)
                                <div class="tema-item {{ $tema->completado ? 'tema-completado' : 'tema-pendiente' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                @if($tema->completado)
                                                <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                                                @else
                                                <i class="fas fa-circle text-warning me-3"></i>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">
                                                        Tema {{ $temaIndex + 1 }}: {{ $tema->Nombre }}
                                                        @if($tema->completado)
                                                        <span class="badge badge-completado ms-2">Completado</span>
                                                        @else
                                                        <span class="badge badge-pendiente ms-2">Pendiente</span>
                                                        @endif
                                                    </h6>
                                                    <p class="mb-0 small text-muted">{{ $tema->Descripcion }}</p>
                                                    @if($tema->Contenido)
                                                    <small class="text-info">
                                                        <i class="fas fa-file me-1"></i>{{ $tema->Contenido }}
                                                    </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            @if($tema->completado)
                                            <span class="badge bg-success me-2">
                                                <i class="fas fa-check"></i> Completado
                                            </span>
                                            @else
                                            <button class="btn btn-primary btn-sm" onclick="comenzarTema({{ $tema->Id_tema }})">
                                                <i class="fas fa-play me-1"></i>Comenzar
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluaciones Pendientes -->
       <!-- Evaluaciones Pendientes CORREGIDA -->
@if(count($evaluaciones_pendientes) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Evaluaciones Pendientes
                    <span class="badge bg-danger ms-2">{{ count($evaluaciones_pendientes) }}</span>
                </h4>
            </div>
            <div class="card-body">
                @foreach($evaluaciones_pendientes as $evaluacion)
                <div class="evaluacion-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-warning mb-1">
                                <i class="fas fa-tasks me-2"></i>{{ $evaluacion->Tipo }}
                            </h6>
                            <p class="mb-1 small">
                                <strong>Módulo:</strong> {{ $evaluacion->modulo_nombre }}
                            </p>
                            <p class="mb-1 small">
                                <strong>Puntaje máximo:</strong> {{ $evaluacion->Puntaje_maximo }} puntos
                            </p>
                            <p class="mb-0 small text-danger">
                                <i class="fas fa-clock me-1"></i>
                                Vence: {{ \Carbon\Carbon::parse($evaluacion->Fecha_fin)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <!-- BOTÓN CORREGIDO -->
                           <!-- Botón corregido -->
<a href="{{ route('estudiante.evaluacion.ver', $evaluacion->Id_evaluacion) }}" 
   class="btn btn-warning btn-sm">
    <i class="fas fa-play me-1"></i>Comenzar
</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@else
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Sin Evaluaciones Pendientes
                </h4>
            </div>
            <div class="card-body text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">¡Excelente trabajo!</h5>
                <p class="text-muted">No tienes evaluaciones pendientes en este momento.</p>
            </div>
        </div>
    </div>
</div>
@endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function comenzarTema(idTema) {
    // Mostrar confirmación antes de marcar como completado
    if (confirm('¿Estás seguro de que quieres marcar este tema como completado?\n\n¡Ganarás 10 puntos!')) {
        completarTema(idTema);
    }
}


</script>
<script>
function completarTema(idTema) {
    console.log("🎯 Iniciando completado del tema:", idTema);
    
    if (!confirm('¿Marcar este tema como completado?\n¡Ganarás 10 puntos!')) {
        return;
    }

    // Mostrar loading
    const boton = event.target;
    const textoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completando...';
    boton.disabled = true;

    // Simple fetch request
    fetch('/estudiante/curso/{{ $curso->Id_curso }}/completar-tema/' + idTema, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log("📨 Respuesta recibida, status:", response.status);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("📊 Datos recibidos:", data);
        
        if (data.success) {
            alert('✅ ' + data.message);
            console.log("🔄 Recargando página en 2 segundos...");
            
            // Recargar después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            alert('❌ ' + data.message);
            // Restaurar botón
            boton.innerHTML = textoOriginal;
            boton.disabled = false;
        }
    })
    .catch(error => {
        console.error('💥 Error:', error);
        alert('❌ Error de conexión. Revisa la consola.');
        // Restaurar botón
        boton.innerHTML = textoOriginal;
        boton.disabled = false;
    });
}
</script>
<script>
function comenzarEvaluacion(idEvaluacion) {
    alert('🔔 Función en desarrollo: Iniciando evaluación ' + idEvaluacion);
}

    // Efectos visuales
    $(document).ready(function() {
        // Animación para los módulos
        $('.modulo-card').hover(
            function() {
                $(this).css('transform', 'translateY(-5px)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
            }
        );
    });
 </script>
 <script>
  
function comenzarTema(idTema) {
    // Mostrar confirmación antes de marcar como completado
    if (confirm('¿Estás seguro de que quieres marcar este tema como completado?\n\n¡Ganarás 10 puntos!')) {
        completarTema(idTema);
    }
}

function completarTema(idTema) {
    console.log("🎯 Iniciando completado del tema:", idTema);
    
    if (!confirm('¿Marcar este tema como completado?\n¡Ganarás 10 puntos!')) {
        return;
    }

    // Mostrar loading
    const boton = event.target;
    const textoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completando...';
    boton.disabled = true;

    // Simple fetch request
    fetch('/estudiante/curso/{{ $curso->Id_curso }}/completar-tema/' + idTema, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log("📨 Respuesta recibida, status:", response.status);
        if (!response.ok) {
            throw new Error('Error HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("📊 Datos recibidos:", data);
        
        if (data.success) {
            alert('✅ ' + data.message);
            console.log("🔄 Recargando página en 2 segundos...");
            
            // Recargar después de 2 segundos
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            alert('❌ ' + data.message);
            // Restaurar botón
            boton.innerHTML = textoOriginal;
            boton.disabled = false;
        }
    })
    .catch(error => {
        console.error('💥 Error:', error);
        alert('❌ Error de conexión. Revisa la consola.');
        // Restaurar botón
        boton.innerHTML = textoOriginal;
        boton.disabled = false;
    });
}

// Función actualizada para evaluaciones
function comenzarEvaluacion(idEvaluacion) {
    // Redirigir a la página de evaluación
    window.location.href = '/estudiante/evaluacion/' + idEvaluacion;
}

// Efectos visuales
$(document).ready(function() {
    // Animación para los módulos
    $('.modulo-card').hover(
        function() {
            $(this).css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
});
</script>
</body>
</html>