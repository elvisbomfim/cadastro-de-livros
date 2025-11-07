<?php

namespace App\Repositories\Eloquent;

use Core\Domain\Repository\RelatorioRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RelatorioRepository implements RelatorioRepositoryInterface
{
    private function isMySQL(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }

    public function livrosPorCategoria(): array
    {
        if ($this->isMySQL()) {
            $result = DB::select('CALL sp_livros_por_categoria()');
        } else {
            // Para SQLite (testes), usar query direta
            $result = DB::select("
                SELECT 
                    ass.descricao AS categoria,
                    COUNT(DISTINCT l.id) AS total_livros,
                    SUM(l.preco) AS valor_total,
                    AVG(l.preco) AS preco_medio
                FROM assuntos ass
                LEFT JOIN livro_assuntos las ON ass.id = las.assunto_id
                LEFT JOIN livros l ON las.livro_id = l.id
                GROUP BY ass.id, ass.descricao
                ORDER BY total_livros DESC
            ");
        }
        return array_map(fn($item) => (array) $item, $result);
    }

    public function fichaDetalhadaLivro(string $id): ?array
    {
        if ($this->isMySQL()) {
            $result = DB::select('CALL sp_ficha_detalhada_livro(?)', [$id]);
        } else {
            // Para SQLite (testes), usar query direta
            $result = DB::select("
                SELECT 
                    l.id,
                    l.titulo,
                    l.editora,
                    l.edicao,
                    l.ano_publicacao,
                    l.preco,
                    GROUP_CONCAT(DISTINCT a.nome) AS autores,
                    GROUP_CONCAT(DISTINCT ass.descricao) AS assuntos,
                    COUNT(DISTINCT a.id) AS total_autores,
                    COUNT(DISTINCT ass.id) AS total_assuntos
                FROM livros l
                LEFT JOIN livro_autors la ON l.id = la.livro_id
                LEFT JOIN autors a ON la.autor_id = a.id
                LEFT JOIN livro_assuntos las ON l.id = las.livro_id
                LEFT JOIN assuntos ass ON las.assunto_id = ass.id
                WHERE l.id = ?
                GROUP BY l.id, l.titulo, l.editora, l.edicao, l.ano_publicacao, l.preco
            ", [$id]);
        }
        
        if (empty($result)) {
            return null;
        }
        
        return (array) $result[0];
    }

    public function relatorioPorAutor(): array
    {
        if ($this->isMySQL()) {
            $result = DB::select('CALL sp_relatorio_por_autor()');
        } else {
            // Para SQLite (testes), usar query direta
            $result = DB::select("
                SELECT 
                    a.id AS autor_id,
                    a.nome AS autor_nome,
                    COUNT(DISTINCT l.id) AS total_livros,
                    GROUP_CONCAT(DISTINCT l.titulo) AS titulos_livros,
                    GROUP_CONCAT(DISTINCT ass.descricao) AS categorias,
                    SUM(l.preco) AS valor_total,
                    AVG(l.preco) AS preco_medio,
                    MIN(l.ano_publicacao) AS primeiro_livro_ano,
                    MAX(l.ano_publicacao) AS ultimo_livro_ano
                FROM autors a
                LEFT JOIN livro_autors la ON a.id = la.autor_id
                LEFT JOIN livros l ON la.livro_id = l.id
                LEFT JOIN livro_assuntos las ON l.id = las.livro_id
                LEFT JOIN assuntos ass ON las.assunto_id = ass.id
                GROUP BY a.id, a.nome
                ORDER BY total_livros DESC, a.nome
            ");
        }
        return array_map(fn($item) => (array) $item, $result);
    }
}

