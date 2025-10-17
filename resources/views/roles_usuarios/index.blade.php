<!DOCTYPE html>
<html>
<head>
    <title>Roles de Usuario</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Roles Asignados a Usuarios</h1>
    <table>
        <thead>
            <tr>
                <th>ID Asignación</th>
                <th>ID Usuario</th>
                <th>ID Rol</th>
                <th>Fecha de Asignación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rolUsuarios as $rolUsuario)
                <tr>
                    <td>{{ $rolUsuario->id_rol_usuario }}</td>
                    <td>{{ $rolUsuario->id_usuario }}</td>
                    <td>{{ $rolUsuario->id_rol }}</td>
                    <td>{{ $rolUsuario->Fecha_asignacion }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="4">No hay roles asignados a usuarios.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>