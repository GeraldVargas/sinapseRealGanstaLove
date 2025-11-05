<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Recompensas - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sinapse-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-recompensa {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        .card-recompensa:hover {
            transform: translateY(-5px);
        }
        .puntos-badge {
            background: linear-gradient(45deg, #ffc107, #ffd54f);
            color: #333;
            font-weight: bold;
        }
        .tipo-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sinapse-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
            <i class="fas fa-brain me-2"></i>SINAPSE
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="/estudiante/dashboard">
                <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white sinapse-navbar">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold">🎁 Catálogo de Recompensas</h1>
                            <p class="lead mb-0">Canjea tus puntos por increíbles beneficios</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-white rounded p-3 text-dark">
                                <small class="text-muted">Tus Puntos Disponibles</small>
                                <div class="fw-bold fs-2 text-warning">
                                    @if($puntosUsuario)
                                        {{ number_format($puntosUsuario->Total_puntos_actual) }}
                                    @else
                                        0
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-coins me-1"></i>Puntos para canjear
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Pestañas -->
    <ul class="nav nav-tabs" id="recompensasTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="catalogo-tab" data-bs-toggle="tab" data-bs-target="#catalogo">
                <i class="fas fa-gift me-2"></i>Catálogo de Recompensas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial">
                <i class="fas fa-history me-2"></i>Mi Historial de Canjes
            </button>
        </li>
    </ul>

    <div class="tab-content mt-4" id="recompensasTabsContent">
        <!-- Pestaña Catálogo -->
        <div class="tab-pane fade show active" id="catalogo" role="tabpanel">
            @if($recompensas->count() > 0)
            <div class="row">
                @foreach($recompensas as $recompensa)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card card-recompensa h-100 position-relative">
                        <span class="badge tipo-badge 
                            @if($recompensa->Tipo == 'Descuento') bg-success
                            @elseif($recompensa->Tipo == 'Producto') bg-primary
                            @elseif($recompensa->Tipo == 'Acceso') bg-warning text-dark
                            @else bg-info @endif">
                            {{ $recompensa->Tipo }}
                        </span>

                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($recompensa->Tipo == 'Descuento')
                                <i class="fas fa-tag fa-3x text-success"></i>
                                @elseif($recompensa->Tipo == 'Producto')
                                <i class="fas fa-box fa-3x text-primary"></i>
                                @elseif($recompensa->Tipo == 'Acceso')
                                <i class="fas fa-ticket-alt fa-3x text-warning"></i>
                                @else
                                <i class="fas fa-gift fa-3x text-info"></i>
                                @endif
                            </div>

                            <h5 class="card-title text-center">{{ $recompensa->Nombre }}</h5>
                            <p class="card-text text-muted text-center">{{ $recompensa->Descripcion }}</p>

                            <div class="text-center mb-3">
                                <span class="badge puntos-badge py-2 px-3">
                                    <i class="fas fa-coins me-1"></i>
                                    {{ number_format($recompensa->Costo_puntos) }} puntos
                                </span>
                            </div>

                            @if($recompensa->Stock !== null)
                            <div class="text-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-boxes me-1"></i>
                                    {{ $recompensa->Stock }} disponibles
                                </small>
                            </div>
                            @endif

                            <div class="d-grid">
                                @if($puntosUsuario && $puntosUsuario->Total_puntos_actual >= $recompensa->Costo_puntos)
                                    @if($recompensa->Stock === null || $recompensa->Stock > 0)
                                        <button class="btn btn-warning btn-canjar" 
                                                data-recompensa-id="{{ $recompensa->Id_recompensa }}"
                                                data-recompensa-nombre="{{ $recompensa->Nombre }}"
                                                data-recompensa-puntos="{{ $recompensa->Costo_puntos }}">
                                            <i class="fas fa-shopping-cart me-2"></i>Canjear Ahora
                                        </button>
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-times me-2"></i>Agotado
                                        </button>
                                    @endif
                                @else
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-lock me-2"></i>Puntos Insuficientes
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay recompensas disponibles</h5>
                <p class="text-muted">Vuelve pronto para ver nuevas recompensas.</p>
            </div>
            @endif
        </div>

        <!-- Pestaña Historial -->
        <div class="tab-pane fade" id="historial" role="tabpanel">
            @if(count($historialCanjes) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Recompensa</th>
                            <th>Tipo</th>
                            <th>Puntos</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historialCanjes as $canje)
                        <tr>
                            <td>
                                <strong>{{ $canje->recompensa_nombre }}</strong>
                                <br>
                                <small class="text-muted">{{ $canje->recompensa_descripcion }}</small>
                            </td>
                            <td>
                                 <span class="badge 
        @if($canje->estado_texto == 'pendiente') bg-warning
        @elseif($canje->estado_texto == 'aprobado') bg-success
        @elseif($canje->estado_texto == 'entregado') bg-info
        @elseif($canje->estado_texto == 'rechazado') bg-danger
        @else bg-secondary @endif">
        {{ ucfirst($canje->estado_texto) }}
    </span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    -{{ number_format($canje->Puntos_utilizados) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($canje->Fecha_canje)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($canje->Estado == 'pendiente') bg-warning
                                    @elseif($canje->Estado == 'completado') bg-success
                                    @elseif($canje->Estado == 'cancelado') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst($canje->Estado) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aún no has canjeado recompensas</h5>
                <p class="text-muted">¡Canjea tu primera recompensa y aparecerá aquí!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarCanje" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i>Confirmar Canje
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres canjear <strong id="recompensaNombre"></strong> por <strong id="recompensaPuntos"></strong> puntos?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta acción no se puede deshacer.
                </div>
                <form id="formCanjear" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Comentario (opcional)</label>
                        <textarea class="form-control" name="comentario" rows="2" placeholder="Agrega un comentario si lo deseas..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmarCanje">
                    <i class="fas fa-check me-2"></i>Sí, Canjear
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmarCanje'));
    const formCanjear = document.getElementById('formCanjear');
    const btnConfirmar = document.getElementById('btnConfirmarCanje');
    let recompensaId = null;

    // Configurar botones de canje
    document.querySelectorAll('.btn-canjar').forEach(button => {
        button.addEventListener('click', function() {
            recompensaId = this.getAttribute('data-recompensa-id');
            const nombre = this.getAttribute('data-recompensa-nombre');
            const puntos = this.getAttribute('data-recompensa-puntos');
            
            document.getElementById('recompensaNombre').textContent = nombre;
            document.getElementById('recompensaPuntos').textContent = puntos;
            
            formCanjear.action = `/estudiante/recompensas/canjear/${recompensaId}`;
            modalConfirmar.show();
        });
    });

    // Confirmar canje
    btnConfirmar.addEventListener('click', function() {
        formCanjear.submit();
    });
});
</script>
</body>
</html>