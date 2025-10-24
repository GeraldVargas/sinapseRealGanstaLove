@extends('layouts.app')

@section('title', 'Registro - Sinapse')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold text-center mb-6">📝 Registrarse</h1>
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                <input type="text" name="Nombre" value="{{ old('Nombre') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Apellido:</label>
                <input type="text" name="Apellido" value="{{ old('Apellido') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
                <input type="password" name="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña:</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Usuario:</label>
                <select name="tipo_usuario" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Selecciona un rol</option>
                    <option value="Estudiante" {{ old('tipo_usuario') == 'Estudiante' ? 'selected' : '' }}>Estudiante</option>
                    <option value="Docente" {{ old('tipo_usuario') == 'Docente' ? 'selected' : '' }}>Docente</option>
                    <option value="Admin" {{ old('tipo_usuario') == 'Admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 font-semibold">
                Crear Cuenta
            </button>

            <div class="mt-4 text-center">
                <a href="/login" class="text-blue-600 hover:text-blue-800 text-sm">
                    ¿Ya tienes cuenta? Inicia sesión aquí
                </a>
            </div>
        </form>
    </div>
</div>
@endsection