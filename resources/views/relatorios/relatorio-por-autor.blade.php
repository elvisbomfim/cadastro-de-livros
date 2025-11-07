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
        .autor-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
            page-break-inside: avoid;
        }
        .autor-header {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .info-row {
            margin-bottom: 10px;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .livros-list {
            margin-top: 10px;
            padding-left: 20px;
        }
        .livro-item {
            margin-bottom: 5px;
            color: #555;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .stat-box {
            background-color: white;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            min-width: 150px;
        }
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
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
    
    @foreach($dados as $autor)
        @php
            $autor = is_array($autor) ? (object) $autor : $autor;
        @endphp
        <div class="autor-section">
            <div class="autor-header">{{ $autor->autor_nome ?? 'Autor sem nome' }}</div>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-label">Total de Livros</div>
                    <div class="stat-value">{{ $autor->total_livros ?? 0 }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Valor Total</div>
                    <div class="stat-value">R$ {{ number_format($autor->valor_total ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Preço Médio</div>
                    <div class="stat-value">R$ {{ number_format($autor->preco_medio ?? 0, 2, ',', '.') }}</div>
                </div>
            </div>
            
            @if($autor->titulos_livros)
                <div class="info-row">
                    <div class="info-label">Livros:</div>
                    <div class="info-value">
                        <div class="livros-list">
                            @foreach(explode(' | ', $autor->titulos_livros) as $titulo)
                                <div class="livro-item">• {{ trim($titulo) }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            @if($autor->categorias)
                <div class="info-row">
                    <div class="info-label">Categorias:</div>
                    <div class="info-value">{{ $autor->categorias }}</div>
                </div>
            @endif
            
            @if($autor->primeiro_livro_ano && $autor->ultimo_livro_ano)
                <div class="info-row">
                    <div class="info-label">Período de Publicação:</div>
                    <div class="info-value">{{ $autor->primeiro_livro_ano }} - {{ $autor->ultimo_livro_ano }}</div>
                </div>
            @endif
        </div>
    @endforeach
    
    <div class="footer">
        <p>Gerado em {{ date('d/m/Y H:i:s') }}</p>
        <p>Total de Autores: {{ count($dados) }}</p>
    </div>
</body>
</html>

