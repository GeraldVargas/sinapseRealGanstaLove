<!DOCTYPE html>
<html>
<head>
    <title>Lista de Usuarios</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    <table>
        <thead>
            <tr>
                <th>ID Usuario</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Fecha de Registro</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuario }}</td>
                    <td>{{ $usuario->Nombre }}</td>
                    <td>{{ $usuario->Apellido }}</td>
                    <td>{{ $usuario->Email }}</td>
                    <td>{{ $usuario->Fecha_registro }}</td>
                    <td>{{ $usuario->Estado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>