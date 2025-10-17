<!DOCTYPE html>
<html>
<head>
    <title>Lista de Permisos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Permisos del Sistema</h1>
    <table>
        <thead>
            <tr>
                <th>ID Permiso</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permisos as $permiso)
                <tr>
                    <td>{{ $permiso->id_permiso }}</td>
                    <td>{{ $permiso->Nombre }}</td>
                    <td>{{ $permiso->Descripcion }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="3">No hay permisos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>