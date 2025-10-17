<!DOCTYPE html>
<html>
<head>
    <title>Lista de Temas</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Lista de Temas</h1>
    <table>
        <thead>
            <tr>
                <th>ID Tema</th>
                <th>ID Módulo</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Orden</th>
                <th>Contenido</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($temas as $tema)
                <tr>
                    <td>{{ $tema->id_tema }}</td>
                    <td>{{ $tema->id_modulo }}</td>
                    <td>{{ $tema->Nombre }}</td>
                    <td>{{ $tema->Descripcion }}</td>
                    <td>{{ $tema->Orden }}</td>
                    <td>{{ $tema->Contenido }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay temas registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>