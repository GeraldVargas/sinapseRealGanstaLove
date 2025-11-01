@extends('layouts.app')

@section('title', 'Monitoreo de Triggers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Estado de los Triggers del Sistema</h4>
                </div>
                <div class="card-body">
                    <!-- Resumen de Triggers -->
                    <div class="row">
                        @foreach($triggers as $trigger)
                        <div class="col-md-4 mb-3">
                            <div class="card {{ $trigger['estado'] ? 'border-success' : 'border-danger' }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $trigger['nombre'] }}</h5>
                                    <p class="card-text">{{ $trigger['descripcion'] }}</p>
                                    <span class="badge badge-{{ $trigger['estado'] ? 'success' : 'danger' }}">
                                        {{ $trigger['estado'] ? 'ACTIVO' : 'INACTIVO' }}
                                    </span>
                                    <small class="text-muted d-block mt-2">Tabla: {{ $trigger['tabla'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Estadísticas de Bitácoras -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Bitácora de Inscripciones</h5>
                                </div>
                                <div class="card-body">
                                    <p>Total registros: {{ $totalInscripciones }}</p>
                                    <p>Última inscripción: {{ $ultimaInscripcion }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Bitácora de Pagos</h5>
                                </div>
                                <div class="card-body">
                                    <p>Total registros: {{ $totalPagos }}</p>
                                    <p>Último pago: {{ $ultimoPago }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pruebas de Funcionalidad -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pruebas de Triggers</h5>
                                </div>
                                <div class="card-body">
                                    <button class="btn btn-primary" onclick="probarTriggerCupos()">
                                        Probar Trigger de Cupos
                                    </button>
                                    <button class="btn btn-info" onclick="probarTriggerPuntos()">
                                        Probar Trigger de Puntos
                                    </button>
                                    <button class="btn btn-warning" onclick="probarTriggerCanje()">
                                        Probar Trigger de Canje
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function probarTriggerCupos() {
    $.post('/admin/triggers/probar-cupos', {
        _token: '{{ csrf_token() }}'
    }, function(response) {
        alert(response.message);
    });
}

function probarTriggerPuntos() {
    $.post('/admin/triggers/probar-puntos', {
        _token: '{{ csrf_token() }}'
    }, function(response) {
        alert(response.message);
    });
}

function probarTriggerCanje() {
    $.post('/admin/triggers/probar-canje', {
        _token: '{{ csrf_token() }}'
    }, function(response) {
        alert(response.message);
    });
}
</script>
@endsection