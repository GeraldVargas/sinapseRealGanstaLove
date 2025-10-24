<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Cursos - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sinapse-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-course {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card-course:hover {
            transform: translateY(-5px);
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
                <a class="nav-link" href="/estudiante">Estudiante</a>
                <a class="nav-link" href="/docente">Docente</a>
                <a class="nav-link" href="/admin">Admin</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Todos los Cursos</h1>
                <p class="text-muted">Cursos disponibles en la plataforma</p>
            </div>
        </div>

        <div class="row">
            @forelse($cursos as $curso)
            <div class="col-md-4 mb-4">
                <div class="card card-course h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $curso->Titulo }}</h5>
                        <p class="card-text text-muted">{{ $curso->Descripcion }}</p>
                        <div class="mb-3">
                            <span class="badge bg-primary me-2">{{ $curso->Duracion }}h</span>
                            <span class="badge bg-success">${{ number_format($curso->Costo, 2) }}</span>
                        </div>
                        <button class="btn btn-primary w-100">Inscribirse</button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-book fa-3x mb-3"></i>
                    <h4>No hay cursos disponibles</h4>
                    <p>No se encontraron cursos en la base de datos.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>