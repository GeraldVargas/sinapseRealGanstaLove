@extends('layouts.app')

@section('title', 'Mis Cursos - Estudiante')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">🎓 Bienvenido Estudiante</h2>
            <p class="text-gray-600 mb-6">Hola, {{ $user->Nombre }} {{ $user->Apellido }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="font-semibold text-blue-800 text-lg mb-2">Programación Básica</h3>
                    <p class="text-blue-600 text-sm">Progreso: 65%</p>
                    <div class="mt-2 bg-blue-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <h3 class="font-semibold text-green-800 text-lg mb-2">Diseño UX/UI</h3>
                    <p class="text-green-600 text-sm">Progreso: 30%</p>
                    <div class="mt-2 bg-green-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 30%"></div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <h3 class="font-semibold text-purple-800 text-lg mb-2">Base de Datos</h3>
                    <p class="text-purple-600 text-sm">Progreso: 10%</p>
                    <div class="mt-2 bg-purple-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection