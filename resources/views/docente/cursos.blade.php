@extends('layouts.app')

@section('title', 'Mis Cursos - Docente')

@section('content')
<div class="container">
    <h1>Mis Cursos</h1>
    
    <div class="row">
        @foreach($cursos as $curso)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $curso['nombre'] }}</h5>
                    <p class="card-text">
                        <strong>Código:</strong> {{ $curso['codigo'] }}<br>
                        <strong>Horario:</strong> {{ $curso['horario'] }}<br>
                        <strong>Aula:</strong> {{ $curso['aula'] }}<br>
                        <strong>Estudiantes:</strong> {{ $curso['estudiantes_inscritos'] }}
                    </p>
                    <a href="#" class="btn btn-primary">Gestionar Curso</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection