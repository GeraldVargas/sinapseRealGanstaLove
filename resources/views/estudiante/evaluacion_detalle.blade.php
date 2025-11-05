<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $evaluacion->Tipo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/estudiante/dashboard">SINAPSE</a>
    </div>
</nav>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Información de la evaluación -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>{{ $evaluacion->Tipo }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Curso:</strong> {{ $evaluacion->curso_titulo }}</p>
                    <p><strong>Módulo:</strong> {{ $evaluacion->modulo_nombre }}</p>
                    <p><strong>Puntaje máximo:</strong> {{ $evaluacion->Puntaje_maximo }} puntos</p>
                    <p><strong>Fecha límite:</strong> {{ \Carbon\Carbon::parse($evaluacion->Fecha_fin)->format('d/m/Y H:i') }}</p>
                    
                    @if($entregaExistente)
                        <div class="alert alert-info">
                            <h5>✅ Ya enviaste esta tarea</h5>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-{{ $entregaExistente->Estado == 'calificado' ? 'success' : 'warning' }}">
                                    {{ ucfirst($entregaExistente->Estado) }}
                                </span>
                            </p>
                            @if($entregaExistente->Puntos_asignados)
                                <p><strong>Puntos obtenidos:</strong> {{ $entregaExistente->Puntos_asignados }}/{{ $evaluacion->Puntaje_maximo }}</p>
                            @endif
                            @if($entregaExistente->Comentario_docente)
                                <p><strong>Comentario del docente:</strong> {{ $entregaExistente->Comentario_docente }}</p>
                            @endif
                        </div>
                    @else
                        <!-- Formulario de entrega -->
                        <form action="{{ route('estudiante.entrega.enviar', $evaluacion->Id_evaluacion) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Descripción de tu entrega *</label>
                                <textarea class="form-control" name="descripcion" rows="4" required placeholder="Describe tu trabajo..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Archivo (opcional)</label>
                                <input type="file" class="form-control" name="archivo" accept=".pdf,.doc,.docx,.zip,.rar">
                                <small class="text-muted">Formatos permitidos: PDF, Word, ZIP (Máx. 10MB)</small>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Entrega
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>