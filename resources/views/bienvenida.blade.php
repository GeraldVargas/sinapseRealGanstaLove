<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida - Sinapse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-purple-600">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-md">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">🎓 Sinapse</h1>
            <p class="text-gray-600 mb-6">Plataforma Educativa con Sistema de Recompensas</p>
            
            <div class="space-y-4">
                <a href="{{ route('login') }}" 
                   class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" 
                   class="block w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                    Registrarse
                </a>
            </div>
            
            <div class="mt-8 text-sm text-gray-500">
                <p>Accede a cursos, gana puntos y canjea recompensas</p>
            </div>
        </div>
    </div>
</body>
</html>