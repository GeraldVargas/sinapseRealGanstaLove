<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Insignias - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sinapse-header {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .badge-dificultad-facil {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .badge-dificultad-medio {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            color: black;
        }
        .badge-dificultad-dificil {
            background: linear-gradient(45deg, #dc3545, #e83e8c);
            color: white;
        }
        .insignia-card {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            border: 3px solid #ffc107;
            transition: transform 0.3s ease;
        }
        .insignia-card:hover {
            transform: translateY(-5px);
        }
        .insignia-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d4af37;
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
                        <h1>Lista de Insignias</h1>
                        <p class="text-muted">{{ $insignias->count() }} insignias disponibles</p>
                    </div>
                    <a href="/" class="btn btn-warning">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Vista en Tarjetas -->
        <div class="row mb-5">
            @foreach($insignias as $insignia)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="insignia-card">
                    <div class="insignia-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4>{{ $insignia->Nombre }}</h4>
                    <p class="text-muted mb-3">{{ $insignia->Descripcion }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-dark me-2">
                            <i class="fas fa-star me-1"></i>{{ $insignia->Valor_Puntos }} pts
                        </span>
                        
                        @if($insignia->Dificultad == 'Fácil')
                            <span class="badge badge-dificultad-facil">Fácil</span>
                        @elseif($insignia->Dificultad == 'Medio')
                            <span class="badge badge-dificultad-medio">Medio</span>
                        @else
                            <span class="badge badge-dificultad-dificil">Difícil</span>
                        @endif
                    </div>
                    
                    <small class="text-muted">
                        Categoría: {{ $insignia->Categoria }}
                    </small>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Vista en Tabla -->
        <div class="card card-custom">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-table me-2"></i>Vista en Tabla</h4>
            </div>
            <div class="card-body">
                @if($insignias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>Insignia</th>
                                <th>Descripción</th>
                                <th>Puntos</th>
                                <th>Dificultad</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($insignias as $insignia)
                            <tr>
                                <td>{{ $insignia->Id_insignia }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 text-warning">
                                            <i class="fas fa-medal fa-2x"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $insignia->Nombre }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $insignia->Descripcion }}</td>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ $insignia->Valor_Puntos }} pts
                                    </span>
                                </td>
                                <td>
                                    @if($insignia->Dificultad == 'Fácil')
                                        <span class="badge badge-dificultad-facil">{{ $insignia->Dificultad }}</span>
                                    @elseif($insignia->Dificultad == 'Medio')
                                        <span class="badge badge-dificultad-medio">{{ $insignia->Dificultad }}</span>
                                    @else
                                        <span class="badge badge-dificultad-dificil">{{ $insignia->Dificultad }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $insignia->Categoria }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" title="Ver insignia">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Editar insignia">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-medal fa-4x text-muted mb-3"></i>
                    <h4>No hay insignias disponibles</h4>
                    <p class="text-muted">No se encontraron insignias en el sistema.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>