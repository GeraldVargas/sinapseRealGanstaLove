<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Puntos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Gestión de Puntos</h1>
    <table>
        <thead>
            <tr>
                <th>ID Gestión</th>
                <th>ID Usuario</th>
                <th>ID Ranking</th>
                <th>Puntos Actuales</th>
                <th>Saldo Usado</th>
                <th>Puntos Acumulados</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($puntos as $punto)
                <tr>
                    <td>{{ $punto->id_gestion }}</td>
                    <td>{{ $punto->id_usuario }}</td>
                    <td>{{ $punto->id_ranking }}</td>
                    <td>{{ $punto->Total_puntos_actual }}</td>
                    <td>{{ $punto->Total_saldo_usado }}</td>
                    <td>{{ $punto->Total_puntos_acumulados }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay registros de puntos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>