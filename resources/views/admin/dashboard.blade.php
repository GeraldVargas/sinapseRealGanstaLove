<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sinapse-navbar {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/admin/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    Admin: {{ $usuario->Nombre }}
                </span>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-dashboard text-white sinapse-navbar">
                    <div class="card-body text-center py-4">
                        <h1 class="display-5 fw-bold">¡Bienvenido, Administrador {{ $usuario->Nombre }}!</h1>
                        <p class="lead mb-0">Panel de control del sistema</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-dashboard">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-cog fa-4x text-danger mb-3"></i>
                        <h3>Área Administrativa en Desarrollo</h3>
                        <p class="text-muted">Esta sección estará disponible próximamente.</p>
                        <p>Aquí podrás gestionar usuarios, cursos, insignias y configuraciones del sistema.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>