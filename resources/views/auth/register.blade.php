<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SINAPSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }
        
        .sinapse-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="sinapse-logo">
                <i class="fas fa-brain fa-3x text-primary mb-3"></i>
                <h2 class="fw-bold">Crear Cuenta</h2>
                <p class="text-muted">Únete a SINAPSE</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <div class="mb-4">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                    <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                        <option value="">Seleccionar tipo de usuario...</option>
                        <option value="Estudiante">Estudiante</option>
                        <option value="Docente">Docente</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3 py-2 fw-bold">Crear Cuenta</button>
            </form>
            
            <div class="text-center">
                <p class="mb-0">¿Ya tienes cuenta? <a href="{{ route('login.form') }}" class="text-decoration-none">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>