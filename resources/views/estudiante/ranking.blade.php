<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .ranking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .top-3-card {
            border: 3px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(45deg, #FFD700, #FFED4E) border-box;
        }
        .top-1 { border-color: #FFD700; background: #FFF9E6; }
        .top-2 { border-color: #C0C0C0; background: #F8F9FA; }
        .top-3 { border-color: #CD7F32; background: #FAF3EB; }
        .usuario-actual {
            background: linear-gradient(45deg, #667eea, #764ba2) !important;
            color: white !important;
        }
        .medal { font-size: 2rem; }
        .top-1 .medal { color: #FFD700; }
        .top-2 .medal { color: #C0C0C0; }
        .top-3 .medal { color: #CD7F32; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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

    <!-- Header -->
    <div class="ranking-header">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">
                <i class="fas fa-trophy me-3"></i>Ranking Mensual
            </h1>
            <p class="lead">Los estudiantes más destacados de SINAPSE</p>
            <div class="row mt-4">
                <div class="col-md-6 offset-md-3">
                    <div class="bg-white rounded p-3 text-dark">
                        <small class="text-muted">Periodo Actual</small>
                        <h4 class="text-primary mb-0">{{ \Carbon\Carbon::parse($periodo . '-01')->translatedFormat('F Y') }}</h4>
                        <small class="text-muted">{{ $total_participantes }} participantes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Mi Posición -->
        @if($miPosicion)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-primary shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-user me-2"></i>Tu Posición en el Ranking
                        </h5>
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h2 class="display-3 text-warning">#{{ $miPosicion->Posicion }}</h2>
                                <small class="text-muted">de {{ $total_participantes }} participantes</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-success">{{ $miPosicion->Total_puntos_acumulados }}</h4>
                                <small class="text-muted">puntos acumulados</small>
                            </div>
                            <div class="col-md-4">
                                <h5>{{ $miPosicion->Nombre }} {{ $miPosicion->Apellido }}</h5>
                                <small class="text-muted">{{ $miPosicion->Email }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Top 3 -->
        @if(count($ranking) >= 3)
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card top-3-card top-2 text-center h-100">
                    <div class="card-body">
                        <div class="medal mb-3">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h2 class="text-muted">#2</h2>
                        <h5 class="card-title">{{ $ranking[1]->Nombre }} {{ $ranking[1]->Apellido }}</h5>
                        <p class="card-text">
                            <span class="h4 text-success">{{ $ranking[1]->Total_puntos_acumulados }}</span><br>
                            <small class="text-muted">puntos</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card top-3-card top-1 text-center h-100 shadow-lg">
                    <div class="card-body">
                        <div class="medal mb-3">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h2 class="text-warning">#1</h2>
                        <h5 class="card-title">{{ $ranking[0]->Nombre }} {{ $ranking[0]->Apellido }}</h5>
                        <p class="card-text">
                            <span class="h3 text-success">{{ $ranking[0]->Total_puntos_acumulados }}</span><br>
                            <small class="text-muted">puntos</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card top-3-card top-3 text-center h-100">
                    <div class="card-body">
                        <div class="medal mb-3">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h2 class="text-warning">#3</h2>
                        <h5 class="card-title">{{ $ranking[2]->Nombre }} {{ $ranking[2]->Apellido }}</h5>
                        <p class="card-text">
                            <span class="h4 text-success">{{ $ranking[2]->Total_puntos_acumulados }}</span><br>
                            <small class="text-muted">puntos</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Ranking Completo -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-list-ol me-2"></i>Ranking Completo
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="80">Posición</th>
                                        <th>Estudiante</th>
                                        <th width="150">Puntos</th>
                                        <th width="120">Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ranking as $index => $participante)
                                    <tr class="{{ $participante->es_usuario_actual ? 'usuario-actual' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($participante->Posicion <= 3)
                                                <i class="fas fa-trophy me-2 
                                                    @if($participante->Posicion == 1) text-warning
                                                    @elseif($participante->Posicion == 2) text-muted
                                                    @else text-warning @endif">
                                                </i>
                                                @endif
                                                <strong>#{{ $participante->Posicion }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px; font-weight: bold;">
                                                    {{ substr($participante->Nombre, 0, 1) }}{{ substr($participante->Apellido, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $participante->Nombre }} {{ $participante->Apellido }}</strong>
                                                    <br>
                                                    <small class="{{ $participante->es_usuario_actual ? 'text-light' : 'text-muted' }}">
                                                        {{ $participante->Email }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6">
                                                {{ $participante->Total_puntos_acumulados }} pts
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $nivel = 'Principiante';
                                                if ($participante->Total_puntos_acumulados >= 500) $nivel = 'Experto';
                                                elseif ($participante->Total_puntos_acumulados >= 200) $nivel = 'Avanzado';
                                                elseif ($participante->Total_puntos_acumulados >= 100) $nivel = 'Intermedio';
                                            @endphp
                                            <span class="badge bg-info">{{ $nivel }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>