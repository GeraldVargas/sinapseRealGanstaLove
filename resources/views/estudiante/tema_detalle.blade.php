<!-- resources/views/estudiante/tema_detalle.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tema->Nombre }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .content-area {
            line-height: 1.6;
            font-size: 1.1rem;
        }
        .content-area h1, .content-area h2, .content-area h3 { 
            margin-top: 1.5rem; 
            margin-bottom: 1rem; 
            color: #2c3e50;
        }
        .content-area p { margin-bottom: 1rem; }
        .content-area ul, .content-area ol { 
            margin-bottom: 1rem; 
            padding-left: 2rem;
        }
        .content-area code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
        }
        .content-area pre {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1rem;
        }
        .sinapse-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
<!-- Navbar (igual que en tu dashboard) -->
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="/ranking">
                <i class="fas fa-trophy me-1"></i>Ranking
            </a>
            <a class="nav-link" href="{{ route('estudiante.recompensas') }}">
                <i class="fas fa-gift me-1"></i>Canjear Recompensas
            </a>
            <a class="nav-link" href="{{ route('estudiante.explorar_cursos') }}">
                <i class="fas fa-search me-1"></i>Explorar Cursos
            </a>
        </div>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="user-avatar d-inline-flex me-2" style="width: 35px; height: 35px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                        {{ substr($usuario->Nombre, 0, 1) }}{{ substr($usuario->Apellido, 0, 1) }}
                    </div>
                    {{ $usuario->Nombre }}
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/estudiante/dashboard"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('estudiante.recompensas') }}"><i class="fas fa-gift me-2"></i>Canjear Recompensas</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Navegación de temas -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Temas del Módulo</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($temas_modulo as $temaItem)
                        <a href="{{ route('estudiante.curso.ver-tema', ['idCurso' => $idCurso, 'idTema' => $temaItem->Id_tema]) }}" 
                           class="list-group-item list-group-item-action {{ $temaItem->Id_tema == $tema->Id_tema ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($temaItem->completado)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    @else
                                    <i class="fas fa-circle text-secondary me-2"></i>
                                    @endif
                                    <span class="{{ $temaItem->Id_tema == $tema->Id_tema ? 'text-white' : '' }}">
                                        {{ $temaItem->Nombre }}
                                    </span>
                                </div>
                                <span class="badge bg-{{ $temaItem->Id_tema == $tema->Id_tema ? 'light text-dark' : 'primary' }}">
                                    {{ $temaItem->Orden }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Información del curso -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Curso:</strong> {{ $tema->curso_titulo }}</p>
                    <p class="mb-2"><strong>Módulo:</strong> {{ $tema->modulo_nombre }}</p>
                    <p class="mb-0"><strong>Tema:</strong> {{ $tema->Nombre }}</p>
                </div>
            </div>
        </div>

        <!-- Contenido del tema -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $tema->Nombre }}</h4>
                        <small class="opacity-75">{{ $tema->modulo_nombre }} • {{ $tema->curso_titulo }}</small>
                    </div>
                    @if($tema->completado)
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check me-1"></i>Completado
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Descripción del tema -->
                    @if($tema->Descripcion)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Descripción</h6>
                        <p class="mb-0">{{ $tema->Descripcion }}</p>
                    </div>
                    @endif

                    <!-- Contenido del tema -->
                    <!-- En tema_detalle.blade.php - ACTUALIZAR la sección de contenido -->
<div class="content-area">
    @if($tema->Tipo_contenido == 'texto' && $tema->Contenido_html)
        <!-- Mostrar contenido HTML del docente -->
        <div class="mb-4">
            {!! $tema->Contenido_html !!}
        </div>
    @elseif($tema->Tipo_contenido == 'video' && $tema->Url_video)
        <!-- Mostrar video -->
        <div class="mb-4">
            <div class="ratio ratio-16x9">
                <iframe src="{{ $tema->Url_video }}" allowfullscreen></iframe>
            </div>
        </div>
    @elseif($tema->Tipo_contenido == 'pdf' && $tema->Archivo_adjunto)
        <!-- Mostrar PDF CORREGIDO -->
        <div class="mb-4">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-file-pdf me-2"></i>
                    <strong>Documento PDF:</strong> {{ $tema->Nombre }}
                </div>
                <a href="{{ asset('storage/temas/' . $tema->Archivo_adjunto) }}" 
                   target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-download me-1"></i>Descargar PDF
                </a>
            </div>
            
            <!-- Visualizador de PDF -->
            <div class="border rounded p-3 bg-light">
                <iframe src="{{ asset('storage/temas/' . $tema->Archivo_adjunto) }}#toolbar=0" 
                        width="100%" height="600px" style="border: none;">
                    Tu navegador no soporta la visualización de PDF. 
                    <a href="{{ asset('storage/temas/' . $tema->Archivo_adjunto) }}" download>
                        Descarga el PDF
                    </a>
                </iframe>
            </div>
        </div>
    @elseif($tema->Tipo_contenido == 'presentacion' && $tema->Archivo_adjunto)
        <!-- Mostrar presentación -->
        <div class="mb-4">
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-file-powerpoint me-2"></i>
                    <strong>Presentación:</strong> {{ $tema->Nombre }}
                </div>
                <a href="{{ asset('storage/temas/' . $tema->Archivo_adjunto) }}" 
                   target="_blank" class="btn btn-warning btn-sm">
                    <i class="fas fa-download me-1"></i>Descargar Presentación
                </a>
            </div>
            <div class="text-center py-4 bg-light rounded">
                <i class="fas fa-file-powerpoint fa-3x text-warning mb-3"></i>
                <p>Descarga la presentación para ver el contenido completo.</p>
            </div>
        </div>
    @elseif($tema->Contenido)
        <!-- Contenido antiguo (para compatibilidad) -->
        <div class="mb-4">
            {!! $tema->Contenido !!}
        </div>
    @else
        <div class="alert alert-warning text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h5>Contenido no disponible</h5>
            <p class="mb-0">El docente aún no ha subido el contenido para este tema.</p>
        </div>
    @endif
</div>

                    <!-- Botones de acción -->
                    <div class="border-top pt-4 mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('estudiante.curso.ver', $idCurso) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Curso
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                @if(!$tema->completado)
                                <form action="{{ route('estudiante.curso.completar-tema', ['idCurso' => $idCurso, 'idTema' => $tema->Id_tema]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check-circle me-2"></i>Marcar como Completado
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-check-circle me-2"></i>Tema Completado
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- En tema_detalle.blade.php - AGREGAR este script al final -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el formulario de completar tema
    const formCompletarTema = document.querySelector('form[action*="completar-tema"]');
    
    if (formCompletarTema) {
        formCompletarTema.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            
            // Enviar solicitud AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    mostrarMensajeExito(data.message, data.puntos_otorgados);
                    
                    // Actualizar interfaz
                    actualizarInterfazTemaCompletado(data);
                    
                    // Recargar la página después de 2 segundos para ver cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarMensajeError(data.message);
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensajeError('Error al procesar la solicitud');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
    
    function mostrarMensajeExito(mensaje, puntos) {
        // Crear alerta de éxito
        const alerta = document.createElement('div');
        alerta.className = 'alert alert-success alert-dismissible fade show';
        alerta.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Éxito!</strong> ${mensaje}
            ${puntos ? `<br><small><strong>+${puntos} puntos</strong> asignados</small>` : ''}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insertar al inicio del card-body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alerta, cardBody.firstChild);
        
        // Desplazar hacia arriba para ver el mensaje
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function mostrarMensajeError(mensaje) {
        const alerta = document.createElement('div');
        alerta.className = 'alert alert-danger alert-dismissible fade show';
        alerta.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alerta, cardBody.firstChild);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    function actualizarInterfazTemaCompletado(data) {
        // Actualizar botón a "Completado"
        const submitButton = document.querySelector('form[action*="completar-tema"] button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Tema Completado';
        submitButton.className = 'btn btn-success btn-lg';
        
        // Actualizar badge en la navegación si existe
        const currentThemeLink = document.querySelector(`.list-group-item.active`);
        if (currentThemeLink) {
            const icon = currentThemeLink.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-check-circle text-success me-2';
            }
        }
        
        // Actualizar porcentaje si se muestra en la página
        const porcentajeElement = document.querySelector('.progress-bar');
        if (porcentajeElement && data.nuevo_porcentaje) {
            porcentajeElement.style.width = data.nuevo_porcentaje + '%';
            porcentajeElement.setAttribute('aria-valuenow', data.nuevo_porcentaje);
            porcentajeElement.textContent = data.nuevo_porcentaje + '%';
        }
    }
});
</script>
</body>
</html>