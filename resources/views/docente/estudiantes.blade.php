@extends('layouts.app')

@section('title', 'Estudiantes - Docente')

@section('content')
<div class="container">
    <h1>Mis Estudiantes</h1>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Calificación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantes as $estudiante)
            <tr>
                <td>{{ $estudiante['nombre'] }}</td>
                <td>{{ $estudiante['email'] }}</td>
                <td>{{ $estudiante['curso'] }}</td>
                <td>{{ $estudiante['calificacion'] }}</td>
                <td>
                    <button class="btn btn-sm btn-info">Ver</button>
                    <button class="btn btn-sm btn-warning">Editar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection