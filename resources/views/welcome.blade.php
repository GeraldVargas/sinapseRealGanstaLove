<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINAPSE - Plataforma de Aprendizaje Inteligente</title>
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 100px 0;
        }
        
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            padding: 2rem;
            text-align: center;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .course-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .btn-sinapse {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-sinapse:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .navbar-brand {
            font-weight: 800;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="/">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="#features">Características</a>
                    <a class="nav-link" href="#courses">Cursos</a>
                    <a class="nav-link" href="/login" class="btn btn-sinapse">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="padding-top: 120px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Aprende, Crece y Triunfa con SINAPSE</h1>
                    <p class="lead mb-4">
                        La plataforma de aprendizaje más innovadora que combina educación de calidad 
                        con gamificación. Desarrolla tus habilidades, gana insignias y lleva tu 
                        conocimiento al siguiente nivel.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/login" class="btn btn-light btn-lg px-4">Iniciar Sesión</a>
                        <a href="/register" class="btn btn-outline-light btn-lg px-4">Registrarse</a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-4">Saber Más</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-graduation-cap fa-10x text-white-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-primary fw-bold">{{ $estadisticas['total_estudiantes'] }}+</h2>
                        <p class="text-muted">Estudiantes Activos</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-success fw-bold">{{ $estadisticas['total_cursos'] }}+</h2>
                        <p class="text-muted">Cursos Disponibles</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-warning fw-bold">{{ $estadisticas['total_docentes'] }}+</h2>
                        <p class="text-muted">Docentes Expertos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">¿Por qué elegir SINAPSE?</h2>
                    <p class="lead text-muted">Descubre las características que nos hacen únicos</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        <h4>Aprendizaje Gamificado</h4>
                        <p class="text-muted">
                            Gana puntos, desbloquea insignias y compite en rankings mientras aprendes. 
                            La gamificación hace que el aprendizaje sea divertido y adictivo.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Seguimiento de Progreso</h4>
                        <p class="text-muted">
                            Visualiza tu avance en tiempo real. Monitorea tu rendimiento, 
                            completación de cursos y mejora continua con nuestras métricas detalladas.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon text-warning">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Comunidad Activa</h4>
                        <p class="text-muted">
                            Conecta con otros estudiantes, comparte conocimientos y resuelve 
                            dudas en nuestra comunidad colaborativa de aprendizaje.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section id="courses" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">Cursos Destacados</h2>
                    <p class="lead text-muted">Explora nuestros cursos más populares</p>
                </div>
            </div>
            <div class="row">
                @foreach($cursos_destacados as $curso)
                <div class="col-md-4 mb-4">
                    <div class="card course-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $curso->Titulo }}</h5>
                            <p class="card-text text-muted">
                                {{ Str::limit($curso->Descripcion, 120) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">{{ $curso->Duracion }}h</span>
                                @if($curso->Costo == 0)
                                    <span class="text-success fw-bold">Gratis</span>
                                @else
                                    <span class="text-success fw-bold">${{ $curso->Costo }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="/login" class="btn btn-sinapse btn-lg">Ver Todos los Cursos</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h2 class="display-5 fw-bold mb-3">¿Listo para comenzar tu viaje de aprendizaje?</h2>
                    <p class="lead mb-4">Únete a miles de estudiantes que ya están transformando su futuro</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="/register" class="btn btn-light btn-lg px-5">Crear Cuenta</a>
                        <a href="/login" class="btn btn-outline-light btn-lg px-5">Iniciar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-brain me-2"></i>SINAPSE</h5>
                    <p class="text-muted">Plataforma de aprendizaje inteligente y gamificado.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; 2024 SINAPSE. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>