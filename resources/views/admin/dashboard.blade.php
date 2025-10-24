<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrador - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        .sinapse-header {
            background: linear-gradient(135deg, var(--warning) 0%, #fd7e14 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-header">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Inicio</a>
                <a class="nav-link" href="/cursos">Cursos</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Header Bienvenida -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card card-dashboard sinapse-header text-dark">
                    <div class="card-body text-center py-4">
                        <h1 class="display-5 fw-bold">¡Bienvenido, Administrador!</h1>
                        <p class="lead mb-0">Panel de control del sistema</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Admin -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-primary">
                    <h3>{{ $estadisticas_admin['usuarios_totales'] }}</h3>
                    <p class="mb-0">Usuarios Totales</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-success">
                    <h3>{{ $estadisticas_admin['cursos_activos'] }}</h3>
                    <p class="mb-0">Cursos Activos</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-info">
                    <h3>${{ number_format($estadisticas_admin['ingresos_mensuales'], 0, ',', '.') }}</h3>
                    <p class="mb-0">Ingresos Mensuales</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card bg-warning">
                    <h3>{{ $estadisticas_admin['nuevos_estudiantes'] }}</h3>
                    <p class="mb-0">Nuevos Estudiantes</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Usuarios Recientes -->
            <div class="col-lg-8">
                <div class="card card-dashboard">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-users me-2"></i>Usuarios Recientes</h4>
                    </div>
                    <div class="card-body">
                        @if($usuarios_recientes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usuarios_recientes as $usuario)
                                        <tr>
                                            <td>{{ $usuario->Id_usuario }}</td>
                                            <td>{{ $usuario->Nombre }} {{ $usuario->Apellido }}</td>
                                            <td>{{ $usuario->Email }}</td>
                                            <td>{{ $usuario->Fecha_registro }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Editar</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay usuarios registrados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="col-lg-4">
                <div class="card card-dashboard">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-cog me-2"></i>Acciones Rápidas</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">Gestionar Usuarios</button>
                            <button class="btn btn-outline-success btn-sm">Gestionar Cursos</button>
                            <button class="btn btn-outline-warning btn-sm">Configurar Sistema</button>
                            <button class="btn btn-outline-info btn-sm">Ver Reportes</button>
                            <button class="btn btn-outline-danger btn-sm">Backup BD</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>