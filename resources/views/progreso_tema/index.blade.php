<!DOCTYPE html>
<html>
<head>
    <title>Progreso de Temas</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Progreso de Temas</h1>
    <table>
        <thead>
            <tr>
                <th>ID Progreso</th>
                <th>ID Tema</th>
                <th>Completado</th>
                <th>Porcentaje</th>
                <th>Fecha Completado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($progresos as $progreso)
                <tr>
                    <td>{{ $progreso->id_progreso_tema }}</td>
                    <td>{{ $progreso->id_tema }}</td>
                    <td>{{ $progreso->Completado }}</td>
                    <td>{{ $progreso->Porcentaje }}%</td>
                    <td>{{ $progreso->Fecha_completado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="5">No hay progresos de tema registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>