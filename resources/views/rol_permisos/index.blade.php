<!DOCTYPE html>
<html>
<head>
    <title>Permisos por Rol</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Permisos Asignados a Roles</h1>
    <table>
        <thead>
            <tr>
                <th>ID Rol</th>
                <th>ID Permiso</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rolPermisos as $rolPermiso)
                <tr>
                    <td>{{ $rolPermiso->id_rol }}</td>
                    <td>{{ $rolPermiso->id_permiso }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="2">No hay permisos asignados a roles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>