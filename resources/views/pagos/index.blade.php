<!DOCTYPE html>
<html>
<head>
    <title>Historial de Pagos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Historial de Pagos</h1>
    <table>
        <thead>
            <tr>
                <th>ID Pago</th>
                <th>ID Inscripción</th>
                <th>Monto</th>
                <th>Método</th>
                <th>Fecha de Pago</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pagos as $pago)
                <tr>
                    <td>{{ $pago->id_pago }}</td>
                    <td>{{ $pago->id_inscripcion }}</td>
                    <td>{{ $pago->Monto }}</td>
                    <td>{{ $pago->Metodo }}</td>
                    <td>{{ $pago->Fecha_pago }}</td>
                    <td>{{ $pago->Estado }}</td>
                </tr>
            @empty
                <tr>
                    {{-- El colspan debe coincidir con el número de columnas --}}
                    <td colspan="6">No hay pagos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>