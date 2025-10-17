<!DOCTYPE html>
<html>
<head>
    <title>Ranking</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Ranking de Usuarios</h1>
    <table>
        <thead>
            <tr>
                <th>ID Ranking</th>
                <th>Posición</th>
                <th>Período</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rankings as $ranking)
                <tr>
                    <td>{{ $ranking->id_ranking }}</td>
                    <td>{{ $ranking->Posicion }}</td>
                    <td>{{ $ranking->Periodo }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="3">El ranking está vacío.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>