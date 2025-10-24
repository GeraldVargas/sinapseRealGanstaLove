@extends('layouts.app')

@section('title', 'Todos los Cursos')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">📚 Todos los Cursos</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-lg mb-2">Programación desde Cero</h3>
                    <p class="text-gray-600 text-sm mb-4">Aprende los fundamentos de la programación</p>
                    <div class="flex justify-between items-center">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Principiante</span>
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                            Inscribirse
                        </button>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-lg mb-2">Diseño UX/UI Avanzado</h3>
                    <p class="text-gray-600 text-sm mb-4">Diseño de interfaces y experiencia de usuario</p>
                    <div class="flex justify-between items-center">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Avanzado</span>
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                            Inscribirse
                        </button>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-lg mb-2">Base de Datos SQL</h3>
                    <p class="text-gray-600 text-sm mb-4">Administración y consultas de bases de datos</p>
                    <div class="flex justify-between items-center">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Intermedio</span>
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                            Inscribirse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection