<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega - {{ $evaluacion->Tipo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .entrega-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-upload:hover {
            border-color: #667eea;
            background-color: #f8f9fa;
        }
        .file-upload.dragover {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .file-preview {
            max-width: 200px;
            max-height: 200px;
        }
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
    <div class="entrega-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">Entrega de Trabajo</h1>
                    <p class="lead mb-0">
                        <strong>Evaluación:</strong> {{ $evaluacion->Tipo }} | 
                        <strong>Módulo:</strong> {{ $evaluacion->modulo_nombre }}
                    </p>
                    <div class="mt-2">
                        <span class="badge bg-light text-primary me-2">
                            <i class="fas fa-star me-1"></i>{{ $evaluacion->Puntaje_maximo }} pts máximo
                        </span>
                        <span class="badge bg-light text-success me-2">
                            <i class="fas fa-clock me-1"></i>Entrega: {{ \Carbon\Carbon::parse($evaluacion->Fecha_fin)->format('d/m/Y H:i') }}
                        </span>
                        @if($entregaExistente)
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-check me-1"></i>Ya entregado
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white rounded p-3 text-dark">
                        <small class="text-muted">Estado</small>
                        <div class="h4 mb-0 {{ $entregaExistente ? 'text-success' : 'text-warning' }}">
                            {{ $entregaExistente ? 'Entregado' : 'Pendiente' }}
                        </div>
                        @if($entregaExistente && $entregaExistente->Estado == 'calificado')
                        <small class="text-success">
                            <i class="fas fa-trophy me-1"></i>{{ $entregaExistente->Puntos_asignados }} puntos
                        </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if($entregaExistente)
        <!-- Ya existe una entrega -->
        <div class="row">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Entrega Realizada
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Detalles de tu entrega:</h6>
                                <p><strong>Archivo:</strong> 
                                    @if($entregaExistente->Archivo)
                                    <a href="{{ asset('storage/entregas/' . $entregaExistente->Archivo) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Descargar
                                    </a>
                                    @else
                                    <span class="text-muted">Sin archivo</span>
                                    @endif
                                </p>
                                <p><strong>Descripción:</strong> {{ $entregaExistente->Descripcion ?? 'Sin descripción' }}</p>
                                <p><strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($entregaExistente->Fecha_entrega)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Estado de calificación:</h6>
                                @if($entregaExistente->Estado == 'calificado')
                                <div class="alert alert-success">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-trophy me-2"></i>Calificado
                                    </h6>
                                    <p class="mb-1"><strong>Puntos obtenidos:</strong> {{ $entregaExistente->Puntos_asignados }}/{{ $evaluacion->Puntaje_maximo }}</p>
                                    @if($entregaExistente->Comentario_docente)
                                    <p class="mb-0"><strong>Comentario del docente:</strong> {{ $entregaExistente->Comentario_docente }}</p>
                                    @endif
                                </div>
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>Pendiente de calificación
                                    <p class="mb-0 mt-2 small">El docente revisará tu trabajo y asignará una calificación.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Formulario de entrega -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-upload me-2"></i>Subir Trabajo
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formEntrega" action="{{ route('estudiante.entrega.enviar', $evaluacion->Id_evaluacion) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Descripción del trabajo *</label>
                                <textarea class="form-control" name="descripcion" rows="4" placeholder="Describe tu trabajo, metodología utilizada, herramientas, etc..." required></textarea>
                                <div class="form-text">Incluye información relevante sobre cómo desarrollaste el trabajo.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Archivo del trabajo</label>
                                <div class="file-upload" id="fileUploadArea">
                                    <input type="file" name="archivo" id="archivo" class="d-none" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.png">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Arrastra tu archivo aquí o haz clic para seleccionar</h5>
                                    <p class="text-muted">Formatos permitidos: PDF, Word, ZIP, JPG, PNG (Máx. 10MB)</p>
                                    <div id="filePreview" class="mt-3"></div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> Una vez enviado el trabajo, no podrás modificarlo. 
                                El docente lo revisará y asignará una calificación con puntos.
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/estudiante/dashboard" class="btn btn-secondary me-md-2">Cancelar</a>
                                <button type="submit" class="btn btn-success" id="btnEnviar">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Trabajo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    // Drag and drop functionality
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('archivo');
    const filePreview = document.getElementById('filePreview');

    fileUploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function(e) {
        handleFiles(this.files);
    });

    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            
            // Validar tamaño (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 10MB.');
                return;
            }

            // Mostrar preview
            filePreview.innerHTML = `
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-file me-3 fa-2x"></i>
                    <div>
                        <strong>${file.name}</strong>
                        <div class="small">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    </div>
                    <button type="button" class="btn-close ms-auto" onclick="clearFile()"></button>
                </div>
            `;
        }
    }

    function clearFile() {
        fileInput.value = '';
        filePreview.innerHTML = '';
    }

    // Validación del formulario
    document.getElementById('formEntrega').addEventListener('submit', function(e) {
        const descripcion = document.querySelector('textarea[name="descripcion"]').value.trim();
        
        if (!descripcion) {
            e.preventDefault();
            alert('Por favor, describe tu trabajo.');
            return;
        }

        document.getElementById('btnEnviar').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
        document.getElementById('btnEnviar').disabled = true;
    });
    </script>
</body>
</html>