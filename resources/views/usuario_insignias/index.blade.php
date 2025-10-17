<!DOCTYPE html>
<html>
<head>
    <title>Insignias de Usuario</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Insignias Obtenidas por Usuarios</h1>
    <table>
        <thead>
            <tr>
                <th>ID Usuario</th>
                <th>ID Insignia</th>
                <th>Fecha de Obtención</th>
                <th>Puntos Obtenidos</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarioInsignias as $usuarioInsignia)
                <tr>
                    <td>{{ $usuarioInsignia->id_usuario }}</td>
                    <td>{{ $usuarioInsignia->id_insignia }}</td>
                    <td>{{ $usuarioInsignia->Fecha_obtencion }}</td>
                    <td>{{ $usuarioInsignia->Puntos_Obtenidos }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="4">No hay insignias asignadas a usuarios.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>