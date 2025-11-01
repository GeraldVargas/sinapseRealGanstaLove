@extends('layouts.app')

@section('title', 'Mis Canjes')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Mis Canjes Realizados</h4>
                    <a href="{{ route('canjes.index') }}" class="btn btn-primary">Volver a Recompensas</a>
                </div>
                <div class="card-body">
                    @if($canjes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Recompensa</th>
                                        <th>Tipo</th>
                                        <th>Fecha de Canje</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($canjes as $canje)
                                    <tr>
                                        <td>{{ $canje->recompensa->Descripc }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $canje->recompensa->tipo }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($canje->Fecha_canje)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($canje->Estado)
                                                <span class="badge badge-success">Completado</span>
                                            @else
                                                <span class="badge badge-warning">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>No has realizado ningún canje aún.</p>
                            <a href="{{ route('canjes.index') }}" class="btn btn-primary">Ver Recompensas Disponibles</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection