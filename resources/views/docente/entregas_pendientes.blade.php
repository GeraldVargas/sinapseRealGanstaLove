<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas Pendientes - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/docente/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/docente/dashboard">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>Entregas Pendientes de Calificación
                            <span class="badge bg-danger ms-2">{{ $totalPendientes }}</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        @if($totalPendientes > 0)
        <div class="row">
            @foreach($entregasPendientes as $entrega)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $entrega->evaluacion_tipo }}</h6>
                        <span class="badge bg-warning">Pendiente</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">{{ $entrega->curso_titulo }}</h6>
                        <p class="card-text small text-muted">Módulo: {{ $entrega->modulo_nombre }}</p>
                        
                        <div class="mb-3">
                            <strong>Estudiante:</strong><br>
                            {{ $entrega->estudiante_nombre }} {{ $entrega->estudiante_apellido }}<br>
                            <small class="text-muted">{{ $entrega->estudiante_email }}</small>
                        </div>

                        <div class="mb-3">
                            <strong>Descripción del trabajo:</strong>
                            <p class="small">{{ $entrega->Descripcion }}</p>
                        </div>

                        @if($entrega->Archivo)
                        <div class="mb-3">
                            <strong>Archivo adjunto:</strong><br>
                            <a href="{{ asset('storage/entregas/' . $entrega->Archivo) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Descargar
                            </a>
                        </div>
                        @endif

                        <div class="small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Entregado: {{ \Carbon\Carbon::parse($entrega->Fecha_entrega)->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalCalificar{{ $entrega->Id_entrega }}">
                            <i class="fas fa-check-circle me-1"></i>Calificar Trabajo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de calificación -->
            <div class="modal fade" id="modalCalificar{{ $entrega->Id_entrega }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Calificar Trabajo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('docente.entrega.calificar', $entrega->Id_entrega) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Puntos asignados (Máx: {{ $entrega->Puntaje_maximo }})</label>
                                    <input type="number" class="form-control" name="puntos" 
                                           min="0" max="{{ $entrega->Puntaje_maximo }}" 
                                           value="0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Comentario para el estudiante</label>
                                    <textarea class="form-control" name="comentario" rows="3" 
                                              placeholder="Retroalimentación sobre el trabajo..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Asignar Calificación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
            <h4 class="text-success">¡No hay entregas pendientes!</h4>
            <p class="text-muted">Todos los trabajos han sido calificados.</p>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>