@extends('layouts.app')

@section('title', 'Canjear Puntos')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Canjear Puntos por Recompensas</h4>
                </div>
                <div class="card-body">
                    <!-- Panel de información de puntos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Tus Puntos</h5>
                                    <h3 id="puntos-actual">{{ $puntosUsuario->Total_puntos_actual ?? 0 }}</h3>
                                    <p>Puntos acumulados: {{ $puntosUsuario->Total_puntos_acumulados ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Recompensas Disponibles</h5>
                                    <h3>{{ $recompensas->count() }}</h3>
                                    <a href="{{ route('canjes.mis_canjes') }}" class="text-white">Ver mis canjes</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de recompensas -->
                    <div class="row">
                        @foreach($recompensas as $recompensa)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">{{ $recompensa->Descripc }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $recompensa->tipo }}</p>
                                    <h4 class="text-primary">{{ $recompensa->Costo_puntos }} puntos</h4>
                                </div>
                                <div class="card-footer">
                                    @if($puntosUsuario && $puntosUsuario->Total_puntos_actual >= $recompensa->Costo_puntos)
                                        <button class="btn btn-success btn-block btn-canje" 
                                                data-recompensa-id="{{ $recompensa->Id_recompe }}"
                                                data-recompensa-nombre="{{ $recompensa->Descripc }}"
                                                data-costo-puntos="{{ $recompensa->Costo_puntos }}">
                                            Canjear Ahora
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-block" disabled>
                                            Puntos Insuficientes
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($recompensas->isEmpty())
                    <div class="alert alert-info">
                        No hay recompensas disponibles en este momento.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Canje</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres canjear <strong id="recompensa-nombre"></strong> por <strong id="recompensa-puntos"></strong> puntos?</p>
                <p>Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-confirmar-canje">Confirmar Canje</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let recompensaSeleccionada = null;

    // Manejar clic en botón de canje
    $('.btn-canje').click(function() {
        recompensaSeleccionada = {
            id: $(this).data('recompensa-id'),
            nombre: $(this).data('recompensa-nombre'),
            costo: $(this).data('costo-puntos')
        };

        $('#recompensa-nombre').text(recompensaSeleccionada.nombre);
        $('#recompensa-puntos').text(recompensaSeleccionada.costo);
        $('#modalConfirmacion').modal('show');
    });

    // Confirmar canje
    $('#btn-confirmar-canje').click(function() {
        if (!recompensaSeleccionada) return;

        $(this).prop('disabled', true).text('Procesando...');

        $.ajax({
            url: '{{ route("canjes.canjear") }}',
            method: 'POST',
            data: {
                recompensa_id: recompensaSeleccionada.id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#modalConfirmacion').modal('hide');
                    alert('¡Canje exitoso! ' + response.message);
                    location.reload(); // Recargar para actualizar puntos
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let error = xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido';
                alert('Error: ' + error);
            },
            complete: function() {
                $('#btn-confirmar-canje').prop('disabled', false).text('Confirmar Canje');
            }
        });
    });

    // Actualizar puntos cada 30 segundos
    setInterval(function() {
        $.get('{{ route("canjes.verificar_puntos") }}', function(data) {
            $('#puntos-actual').text(data.puntos_actual);
        });
    }, 30000);
});
</script>
@endsection