<!DOCTYPE html>
<html>
<head>
    <title>Lista de Cursos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Cursos</h1>
    <table>
        <thead>
            <tr>
                <th>ID Curso</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Duración (hrs)</th>
                <th>Costo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cursos as $curso)
                <tr>
                    <td>{{ $curso->id_curso }}</td>
                    <td>{{ $curso->Titulo }}</td>
                    <td>{{ $curso->Descripcion }}</td>
                    <td>{{ $curso->Duracion }}</td>
                    <td>{{ $curso->Costo }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="5">No hay cursos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>