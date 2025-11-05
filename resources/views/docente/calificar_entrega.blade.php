<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar Entrega - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/docente/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('docente.curso.detalle', $entrega->Id_curso) }}">
                <i class="fas fa-arrow-left me-1"></i>Volver al Curso
            </a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Calificar Entrega
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Información de la entrega -->
                    <div class="mb-4">
                        <h5>Información de la Entrega</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Estudiante:</strong> {{ $entrega->estudiante_nombre }} {{ $entrega->estudiante_apellido }}</p>
                                <p><strong>Curso:</strong> {{ $entrega->curso_titulo }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Evaluación:</strong> {{ $entrega->evaluacion_tipo }}</p>
                                <p><strong>Módulo:</strong> {{ $entrega->modulo_nombre }}</p>
                            </div>
                        </div>
                        <p><strong>Puntaje máximo:</strong> {{ $entrega->Puntaje_maximo }} puntos</p>
                        <p><strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($entrega->Fecha_entrega)->format('d/m/Y H:i') }}</p>
                    </div>

                    <!-- Descripción del estudiante -->
                    @if($entrega->Descripcion)
                    <div class="mb-4">
                        <h5>Descripción del Estudiante</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $entrega->Descripcion }}
                        </div>
                    </div>
                    @endif

                    <!-- Archivo adjunto -->
                    @if($entrega->Archivo)
                    <div class="mb-4">
                        <h5>Archivo Adjunto</h5>
                        <a href="{{ asset('storage/entregas/' . $entrega->Archivo) }}" 
                           target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Descargar Archivo
                        </a>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta entrega no tiene archivo adjunto.
                    </div>
                    @endif

                    <!-- Formulario de calificación -->
                    <form action="{{ route('docente.entrega.calificar.post', $entrega->Id_entrega) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="puntos" class="form-label">
                                <strong>Puntos Asignados *</strong>
                                <small class="text-muted">(Máximo: {{ $entrega->Puntaje_maximo }} puntos)</small>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="puntos" 
                                   name="puntos" 
                                   required 
                                   min="0" 
                                   max="{{ $entrega->Puntaje_maximo }}"
                                   value="{{ old('puntos', $entrega->Puntos_asignados ?? 0) }}"
                                   placeholder="Ingresa los puntos obtenidos">
                            @error('puntos')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comentario" class="form-label">
                                <strong>Comentario para el Estudiante</strong>
                                <small class="text-muted">(Opcional)</small>
                            </label>
                            <textarea class="form-control" 
                                      id="comentario" 
                                      name="comentario" 
                                      rows="4" 
                                      placeholder="Proporciona retroalimentación al estudiante...">{{ old('comentario', $entrega->Comentario_docente ?? '') }}</textarea>
                            @error('comentario')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('docente.curso.detalle', $entrega->Id_curso) }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-check-circle me-2"></i>Calificar Entrega
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>