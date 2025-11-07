<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        .valor {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>Categoria</th>
                <th class="valor">Total de Livros</th>
                <th class="valor">Valor Total</th>
                <th class="valor">Preço Médio</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalLivros = 0;
                $valorTotalGeral = 0;
            @endphp
            @foreach($dados as $item)
                @php
                    $item = is_array($item) ? (object) $item : $item;
                    $totalLivros += $item->total_livros ?? 0;
                    $valorTotalGeral += $item->valor_total ?? 0;
                @endphp
                <tr>
                    <td>{{ $item->categoria ?? 'Sem categoria' }}</td>
                    <td class="valor">{{ $item->total_livros ?? 0 }}</td>
                    <td class="valor">R$ {{ number_format($item->valor_total ?? 0, 2, ',', '.') }}</td>
                    <td class="valor">R$ {{ number_format($item->preco_medio ?? 0, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>TOTAL GERAL</strong></td>
                <td class="valor"><strong>{{ $totalLivros }}</strong></td>
                <td class="valor"><strong>R$ {{ number_format($valorTotalGeral, 2, ',', '.') }}</strong></td>
                <td class="valor">-</td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Gerado em {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

