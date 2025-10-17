<!DOCTYPE html>
<html>
<head>
    <title>Lista de Evaluaciones</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Evaluaciones</h1>
    <table>
        <thead>
            <tr>
                <th>ID Evaluación</th>
                <th>ID Módulo</th>
                <th>Tipo</th>
                <th>Puntaje Máximo</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($evaluaciones as $evaluacion)
                <tr>
                    <td>{{ $evaluacion->id_evaluacion }}</td>
                    <td>{{ $evaluacion->id_modulo }}</td>
                    <td>{{ $evaluacion->Tipo }}</td>
                    <td>{{ $evaluacion->Puntaje_maximo }}</td>
                    <td>{{ $evaluacion->Fecha_inicio }}</td>
                    <td>{{ $evaluacion->Fecha_fin }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay evaluaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>