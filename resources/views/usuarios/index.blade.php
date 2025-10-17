<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    <table>
        <thead>
            <tr>
                {{-- CAMBIA ESTOS TÍTULOS por los de tu tabla 'usuarios' --}}
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Fecha de Creación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    {{-- CAMBIA ESTOS NOMBRES por los de las columnas de tu tabla 'usuarios' --}}
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>