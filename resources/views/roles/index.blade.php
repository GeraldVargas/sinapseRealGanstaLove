<!DOCTYPE html>
<html>
<head>
    <title>Lista de Roles</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Roles del Sistema</h1>
    <table>
        <thead>
            <tr>
                <th>ID Rol</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $rol)
                <tr>
                    <td>{{ $rol->id_rol }}</td>
                    <td>{{ $rol->Nombre }}</td>
                    <td>{{ $rol->Descripcion }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="3">No hay roles registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>