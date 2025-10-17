<!DOCTYPE html>
<html>
<head>
    <title>Lista de Módulos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Módulos</h1>
    <table>
        <thead>
            <tr>
                <th>ID Módulo</th>
                <th>ID Curso</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($modulos as $modulo)
                <tr>
                    <td>{{ $modulo->id_modulo }}</td>
                    <td>{{ $modulo->id_curso }}</td>
                    <td>{{ $modulo->Nombre }}</td>
                    <td>{{ $modulo->Descripcion }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="4">No hay módulos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>