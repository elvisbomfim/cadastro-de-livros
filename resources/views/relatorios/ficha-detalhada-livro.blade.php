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
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .info-row {
            margin-bottom: 15px;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
            color: #2c3e50;
        }
        .info-value {
            flex: 1;
            padding-top: 12px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            margin-right: 5px;
            margin-bottom: 5px;
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
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Título:</div>
            <div class="info-value">{{ $livro->titulo }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Editora:</div>
            <div class="info-value">{{ $livro->editora }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Edição:</div>
            <div class="info-value">{{ $livro->edicao }}ª edição</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ano de Publicação:</div>
            <div class="info-value">{{ $livro->ano_publicacao }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Preço:</div>
            <div class="info-value"><strong>R$ {{ number_format($livro->preco, 2, ',', '.') }}</strong></div>
        </div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Autores:</div>
            <div class="info-value">
                @if($livro->autores)
                    @foreach(explode(', ', $livro->autores) as $autor)
                        <span class="badge">{{ trim($autor) }}</span>
                    @endforeach
                @else
                    <em>Nenhum autor cadastrado</em>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Total de Autores:</div>
            <div class="info-value">{{ $livro->total_autores ?? 0 }}</div>
        </div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Assuntos/Categorias:</div>
            <div class="info-value">
                @if($livro->assuntos)
                    @foreach(explode(', ', $livro->assuntos) as $assunto)
                        <span class="badge" style="background-color: #27ae60;">{{ trim($assunto) }}</span>
                    @endforeach
                @else
                    <em>Nenhum assunto cadastrado</em>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Total de Assuntos:</div>
            <div class="info-value">{{ $livro->total_assuntos ?? 0 }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Gerado em {{ date('d/m/Y H:i:s') }}</p>
        <p>ID do Livro: {{ $livro->id }}</p>
    </div>
</body>
</html>

