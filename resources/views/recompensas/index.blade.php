<!DOCTYPE html>
<html>
<head>
    <title>Catálogo de Recompensas</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Catálogo de Recompensas</h1>
    <table>
        <thead>
            <tr>
                <th>ID Recompensa</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Costo en Puntos</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recompensas as $recompensa)
                <tr>
                    <td>{{ $recompensa->id_recompensa }}</td>
                    <td>{{ $recompensa->Nombre }}</td>
                    <td>{{ $recompensa->Descripcion }}</td>
                    <td>{{ $recompensa->Costo_puntos }}</td>
                    <td>{{ $recompensa->Tipo }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="5">No hay recompensas disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>