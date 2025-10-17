<!DOCTYPE html>
<html>
<head>
    <title>Inscripciones</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Inscripciones</h1>
    <table>
        <thead>
            <tr>
                <th>ID Inscripción</th>
                <th>ID Usuario</th>
                <th>ID Curso</th>
                <th>Fecha de Inscripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inscripciones as $inscripcion)
                <tr>
                    <td>{{ $inscripcion->id_inscripcion }}</td>
                    <td>{{ $inscripcion->id_usuario }}</td>
                    <td>{{ $inscripcion->id_curso }}</td>
                    <td>{{ $inscripcion->Fecha_inscripcion }}</td>
                    <td>{{ $inscripcion->Estado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="5">No hay inscripciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>