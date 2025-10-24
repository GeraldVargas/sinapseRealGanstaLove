<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Cursos - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sinapse-header {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .course-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #17a2b8, #6f42c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        .badge-duration {
            background: #17a2b8;
        }
        .badge-cost {
            background: #28a745;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-header">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Inicio</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Lista de Cursos</h1>
                        <p class="text-muted">{{ $cursos->count() }} cursos disponibles</p>
                    </div>
                    <a href="/" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body">
                @if($cursos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-info">
                            <tr>
                                <th>ID</th>
                                <th>Curso</th>
                                <th>Descripción</th>
                                <th>Duración</th>
                                <th>Costo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cursos as $curso)
                            <tr>
                                <td>{{ $curso->Id_curso }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="course-icon me-3">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $curso->Titulo }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ Str::limit($curso->Descripcion, 80) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-duration">
                                        {{ $curso->Duracion }} horas
                                    </span>
                                </td>
                                <td>
                                    @if($curso->Costo == 0)
                                        <span class="badge bg-success">Gratis</span>
                                    @else
                                        <span class="badge badge-cost">
                                            ${{ number_format($curso->Costo, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($curso->Estado)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" title="Ver curso">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Editar curso">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" title="Eliminar curso">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-3"></i>
                    <h4>No hay cursos disponibles</h4>
                    <p class="text-muted">No se encontraron cursos en el sistema.</p>
                    <a href="/" class="btn btn-primary">Volver al Dashboard</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Vista en Tarjetas (Alternativa) -->
        <div class="row mt-5">
            <div class="col-12 mb-3">
                <h3>Vista en Tarjetas</h3>
            </div>
            @foreach($cursos as $curso)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="course-icon me-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <h5 class="card-title mb-0">{{ $curso->Titulo }}</h5>
                        </div>
                        <p class="card-text text-muted small">
                            {{ Str::limit($curso->Descripcion, 120) }}
                        </p>
                        <div class="mb-3">
                            <span class="badge badge-duration me-2">
                                <i class="fas fa-clock me-1"></i>{{ $curso->Duracion }}h
                            </span>
                            @if($curso->Costo == 0)
                                <span class="badge bg-success">
                                    <i class="fas fa-gift me-1"></i>Gratis
                                </span>
                            @else
                                <span class="badge badge-cost">
                                    <i class="fas fa-dollar-sign me-1"></i>{{ number_format($curso->Costo, 2) }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            @if($curso->Estado)
                                <span class="badge bg-success">Disponible</span>
                            @else
                                <span class="badge bg-secondary">No disponible</span>
                            @endif
                            <div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-play"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>