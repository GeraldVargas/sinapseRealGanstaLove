<!DOCTYPE html>
<html>
<head>
    <title>Actividades Complementarias</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Actividades Complementarias</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Curso</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Puntos Extra</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($actividades as $actividad)
                <tr>
                    <td>{{ $actividad->id_actividad_complementaria }}</td>
                    <td>{{ $actividad->id_curso }}</td>
                    <td>{{ $actividad->Tipo }}</td>
                    <td>{{ $actividad->Descripcion }}</td>
                    <td>{{ $actividad->Puntos_extra }}</td>
                    <td>{{ $actividad->Fecha }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay actividades registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>