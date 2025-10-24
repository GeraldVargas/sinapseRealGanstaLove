@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">👤 Mi Perfil</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Información Personal</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                            <p class="mt-1 text-lg">{{ $user->Nombre }} {{ $user->Apellido }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-lg">{{ $user->Email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID de usuario</label>
                            <p class="mt-1 text-lg">{{ $user->id_usuario }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de registro</label>
                            <p class="mt-1 text-lg">{{ $user->Fecha_registro }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-4">Roles y Permisos</h3>
                    <div class="space-y-3">
                        @foreach($user->roles as $rol)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium">{{ $rol->Nombre }}</span>
                                <span class="text-sm text-gray-500">{{ $rol->Descripc }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-3">Acciones</h4>
                        <div class="space-y-2">
                            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                                Editar Perfil
                            </button>
                            <button class="w-full bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection