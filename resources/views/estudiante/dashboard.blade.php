<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Estudiante - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        .sinapse-navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .insignia-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1rem;
            border: 2px solid #ffc107;
        }
        
        .course-progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar d-inline-flex me-2">
                            {{ substr($usuario->Nombre, 0, 1) }}{{ substr($usuario->Apellido, 0, 1) }}
                        </div>
                        {{ $usuario->Nombre }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
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

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <!-- Header Bienvenida -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-dashboard text-white sinapse-navbar">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-5 fw-bold">¡Bienvenido, {{ $usuario->Nombre }}!</h1>
                                <p class="lead mb-0">Continúa tu viaje de aprendizaje en SINAPSE</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="bg-white rounded p-3 text-dark">
                                    <small class="text-muted">Tus Puntos</small>
                                    <div class="fw-bold fs-3 text-warning">{{ $puntos_totales }} pts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Mis Cursos -->
                <div class="card card-dashboard">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Mis Cursos</h4>
                    </div>
                    <div class="card-body">
                        @if($cursos_inscritos->count() > 0)
                            <div class="row">
                                @foreach($cursos_inscritos as $curso)
                                @php
                                    $progreso = $progresos->where('id_curso', $curso->Id_curso)->first();
                                    $porcentaje = $progreso ? $progreso->Porcentaje : 0;
                                @endphp
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $curso->Titulo }}</h5>
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($curso->Descripcion, 100) }}
                                            </p>
                                            
                                            <!-- Barra de Progreso -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Progreso</small>
                                                    <small class="text-muted">{{ $porcentaje }}%</small>
                                                </div>
                                                <div class="progress course-progress">
                                                    <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $curso->Duracion }}h</span>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary">Continuar</button>
                                                    <button class="btn btn-sm btn-outline-info ms-1">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No estás inscrito en ningún curso todavía.</p>
                                <a href="#" class="btn btn-primary">Explorar Cursos</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progreso General -->
                <div class="card card-dashboard">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Mi Progreso General</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="stat-card bg-primary">
                                    <h3>{{ $cursos_inscritos->count() }}</h3>
                                    <p class="mb-0">Cursos Inscritos</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card bg-success">
                                    <h3>{{ $progresos->where('Porcentaje', 100)->count() }}</h3>
                                    <p class="mb-0">Cursos Completados</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card bg-warning">
                                    <h3>{{ $insignias->count() }}</h3>
                                    <p class="mb-0">Insignias Obtenidas</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="stat-card bg-danger">
                                    <h3>{{ $puntos_totales }}</h3>
                                    <p class="mb-0">Puntos Totales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Mis Insignias -->
                <div class="card card-dashboard">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-trophy me-2"></i>Mis Insignias</h4>
                    </div>
                    <div class="card-body">
                        @if($insignias->count() > 0)
                            <div class="row">
                                @foreach($insignias as $insignia)
                                <div class="col-6 mb-3">
                                    <div class="insignia-card">
                                        <i class="fas fa-medal fa-2x mb-2"></i>
                                        <h6 class="mb-1">{{ $insignia->Nombre }}</h6>
                                        <small class="text-muted">+{{ $insignia->Valor_Puntos }} pts</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-trophy fa-2x text-muted mb-2"></i>
                                <p class="text-muted small">Aún no has ganado insignias. ¡Completa cursos para ganarlas!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Próximas Actividades -->
                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-calendar me-2"></i>Próximas Actividades</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">Hoy</small>
                                    <p class="mb-0 small">Evaluación de Matemáticas</p>
                                </div>
                                <span class="badge bg-warning">15:00</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">Mañana</small>
                                    <p class="mb-0 small">Foro de Programación</p>
                                </div>
                                <span class="badge bg-info">10:00</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-primary">15 Mar</small>
                                    <p class="mb-0 small">Entrega Proyecto Final</p>
                                </div>
                                <span class="badge bg-danger">23:59</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accesos Rápidos -->
                <div class="card card-dashboard">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Accesos Rápidos</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm text-start">
                                <i class="fas fa-search me-2"></i>Explorar Cursos
                            </a>
                            <a href="#" class="btn btn-outline-success btn-sm text-start">
                                <i class="fas fa-tasks me-2"></i>Mis Tareas
                            </a>
                            <a href="#" class="btn btn-outline-warning btn-sm text-start">
                                <i class="fas fa-chart-bar me-2"></i>Mis Calificaciones
                            </a>
                            <a href="#" class="btn btn-outline-info btn-sm text-start">
                                <i class="fas fa-users me-2"></i>Comunidad
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>