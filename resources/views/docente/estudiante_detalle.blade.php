<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Estudiante - SINAPSE</title>
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
                <a class="nav-link" href="{{ route('docente.curso.detalle', $curso->Id_curso) }}">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Curso
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i>
                            Detalle del Estudiante - {{ $estudiante->Nombre }} {{ $estudiante->Apellido }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="student-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                        {{ substr($estudiante->Nombre, 0, 1) }}{{ substr($estudiante->Apellido, 0, 1) }}
                                    </div>
                                    <h5>{{ $estudiante->Nombre }} {{ $estudiante->Apellido }}</h5>
                                    <p class="text-muted">{{ $estudiante->Email }}</p>
                                    <p class="text-muted">ID: {{ $estudiante->Id_usuario }}</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5>Progreso en el Curso: {{ $curso->Titulo }}</h5>
                                
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Progreso General</span>
                                        <span>{{ $progreso->Porcentaje ?? 0 }}%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: {{ $progreso->Porcentaje ?? 0 }}%">
                                            {{ $progreso->Porcentaje ?? 0 }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="border rounded p-2">
                                            <h4 class="text-primary mb-0">{{ $progreso->Modulos_Com ?? 0 }}</h4>
                                            <small class="text-muted">Módulos Completados</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2">
                                            <h4 class="text-success mb-0">{{ $progreso->Temas_Comple ?? 0 }}</h4>
                                            <small class="text-muted">Temas Completados</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2">
                                            <h4 class="text-warning mb-0">{{ $progreso->Evaluaciones ?? 0 }}</h4>
                                            <small class="text-muted">Evaluaciones</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2">
                                            <h4 class="text-info mb-0">{{ $progreso->Actividades_R ?? 0 }}</h4>
                                            <small class="text-muted">Actividades</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h6>Insignias Obtenidas</h6>
                                    @if($insignias->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($insignias as $insignia)
                                            <span class="badge bg-warning text-dark p-2">
                                                <i class="fas fa-medal me-1"></i>{{ $insignia->Nombre }}
                                            </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">El estudiante no ha obtenido insignias aún.</p>
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
</body>
</html>