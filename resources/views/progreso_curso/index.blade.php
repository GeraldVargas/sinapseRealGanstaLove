<!DOCTYPE html>
<html>
<head>
    <title>Progreso de Cursos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Progreso de Cursos</h1>
    <table>
        <thead>
            <tr>
                <th>ID Progreso</th>
                <th>ID Usuario</th>
                <th>ID Curso</th>
                <th>Última Actualización</th>
                <th>Porcentaje</th>
                <th>Nivel</th>
                <th>Módulos Completados</th>
                <th>Temas Completados</th>
                <th>Evaluaciones Superadas</th>
                <th>Actividades Superadas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($progresos as $progreso)
                <tr>
                    <td>{{ $progreso->id_progreso }}</td>
                    <td>{{ $progreso->id_usuario }}</td>
                    <td>{{ $progreso->id_curso }}</td>
                    <td>{{ $progreso->Fecha_actualizacion }}</td>
                    <td>{{ $progreso->Porcentaje }}%</td>
                    <td>{{ $progreso->Nivel }}</td>
                    <td>{{ $progreso->Modulos_completados }}</td>
                    <td>{{ $progreso->Temas_completados }}</td>
                    <td>{{ $progreso->Evaluaciones_superadas }}</td>
                    <td>{{ $progreso->Actividades_superadas }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="10">No hay progresos de curso registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>