<!DOCTYPE html>
<html>
<head>
    <title>Catálogo de Insignias</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Catálogo de Insignias</h1>
    <table>
        <thead>
            <tr>
                <th>ID Insignia</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Valor en Puntos</th>
                <th>Dificultad</th>
                <th>Categoría</th>
                <th>Imagen</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($insignias as $insignia)
                <tr>
                    <td>{{ $insignia->id_insignia }}</td>
                    <td>{{ $insignia->Nombre }}</td>
                    <td>{{ $insignia->Descripcion }}</td>
                    <td>{{ $insignia->Valor_Puntos }}</td>
                    <td>{{ $insignia->Dificultad }}</td>
                    <td>{{ $insignia->Categoria }}</td>
                    <td>{{ $insignia->Imagen }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="7">No hay insignias registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>