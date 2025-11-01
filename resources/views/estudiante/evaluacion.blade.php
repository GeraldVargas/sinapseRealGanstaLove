<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación - {{ $evaluacion->Tipo }} - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .evaluation-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .question-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1.5rem;
        }
        .timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/estudiante/dashboard">
                <i class="fas fa-brain me-2"></i>SINAPSE
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white">
                    <i class="fas fa-clock me-1"></i>
                    <span id="timer">00:00</span>
                </span>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="evaluation-container">
            <!-- Header de la evaluación -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $evaluacion->Tipo }}</h4>
                            <p class="mb-0 small">{{ $evaluacion->modulo_nombre }} - {{ $evaluacion->curso_titulo }}</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-primary fs-6">
                                Puntaje máximo: {{ $evaluacion->Puntaje_maximo }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Responde todas las preguntas antes de enviar la evaluación.
                        Tienes tiempo limitado para completarla.
                    </div>
                </div>
            </div>

            <!-- Formulario de evaluación -->
            <form id="evaluationForm" action="{{ route('estudiante.evaluacion.enviar', $evaluacion->Id_evaluacion) }}" method="POST">
                @csrf
                
                <!-- Pregunta 1 -->
                <div class="card question-card">
                    <div class="card-body">
                        <h5 class="card-title">Pregunta 1</h5>
                        <p class="card-text">¿Cuál es el concepto fundamental de la programación orientada a objetos?</p>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta1" id="p1a" value="a" required>
                            <label class="form-check-label" for="p1a">
                                Herencia y Polimorfismo
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta1" id="p1b" value="b">
                            <label class="form-check-label" for="p1b">
                                Variables y Funciones
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta1" id="p1c" value="c">
                            <label class="form-check-label" for="p1c">
                                Bases de Datos
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pregunta1" id="p1d" value="d">
                            <label class="form-check-label" for="p1d">
                                Diseño Web
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card question-card">
                    <div class="card-body">
                        <h5 class="card-title">Pregunta 2</h5>
                        <p class="card-text">¿Qué significa el principio de encapsulamiento en POO?</p>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta2" id="p2a" value="a" required>
                            <label class="form-check-label" for="p2a">
                                Ocultar los datos internos de una clase
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta2" id="p2b" value="b">
                            <label class="form-check-label" for="p2b">
                                Reutilizar código de otras clases
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="pregunta2" id="p2c" value="c">
                            <label class="form-check-label" for="p2c">
                                Crear múltiples instancias de una clase
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pregunta2" id="p2d" value="d">
                            <label class="form-check-label" for="p2d">
                                Compilar el código más rápido
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 (Texto) -->
                <div class="card question-card">
                    <div class="card-body">
                        <h5 class="card-title">Pregunta 3</h5>
                        <p class="card-text">Explica brevemente qué es el polimorfismo en programación orientada a objetos.</p>
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="pregunta3" rows="4" placeholder="Escribe tu respuesta aquí..." required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('estudiante.curso.ver', $evaluacion->Id_curso ?? '') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i>Enviar Evaluación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Timer simple (30 minutos)
        let timeLeft = 30 * 60; // 30 minutos en segundos
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                document.getElementById('evaluationForm').submit();
            } else {
                timeLeft--;
            }
        }
        
        setInterval(updateTimer, 1000);
        updateTimer(); // Inicializar inmediatamente

        // Prevenir envío duplicado
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';
        });
    </script>
</body>
</html>