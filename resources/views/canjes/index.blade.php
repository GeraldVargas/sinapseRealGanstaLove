<!DOCTYPE html>
<html>
<head>
    <title>Canjes</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Canjes Realizados</h1>
    <table>
        <thead>
            <tr>
                <th>ID Canje</th>
                <th>ID Usuario</th>
                <th>ID Recompensa</th>
                <th>Fecha del Canje</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($canjes as $canje)
                <tr>
                    <td>{{ $canje->id_canje }}</td>
                    <td>{{ $canje->id_usuario }}</td>
                    <td>{{ $canje->id_recompensa }}</td>
                    <td>{{ $canje->Fecha_canje }}</td>
                    <td>{{ $canje->Estado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="5">No hay canjes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>