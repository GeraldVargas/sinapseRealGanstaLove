<!DOCTYPE html>
<html>
<head>
    <title>Progreso de Evaluaciones</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Progreso de Evaluaciones</h1>
    <table>
        <thead>
            <tr>
                <th>ID Progreso</th>
                <th>ID Evaluación</th>
                <th>Puntaje Obtenido</th>
                <th>Porcentaje</th>
                <th>Aprobado</th>
                <th>Fecha Completado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($progresos as $progreso)
                <tr>
                    <td>{{ $progreso->id_progreso_evaluacion }}</td>
                    <td>{{ $progreso->id_evaluacion }}</td>
                    <td>{{ $progreso->Puntaje_obtenido }}</td>
                    <td>{{ $progreso->Porcentaje }}%</td>
                    <td>{{ $progreso->Aprobado }}</td>
                    <td>{{ $progreso->Fecha_completado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay progresos de evaluación registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>