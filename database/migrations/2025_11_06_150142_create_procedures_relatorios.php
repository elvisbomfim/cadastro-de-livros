<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        
        // SQLite não suporta stored procedures
        // Estas procedures são apenas para MySQL
        if ($driver === 'sqlite') {
            return;
        }
        
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_livros_por_categoria");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_ficha_detalhada_livro");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_relatorio_por_autor");

        DB::unprepared("
            CREATE PROCEDURE sp_livros_por_categoria()
            BEGIN
                SELECT 
                    ass.descricao AS categoria,
                    COUNT(DISTINCT l.id) AS total_livros,
                    SUM(l.preco) AS valor_total,
                    AVG(l.preco) AS preco_medio
                FROM assunto ass
                LEFT JOIN livro_assunto las ON ass.id = las.assunto_id
                LEFT JOIN livro l ON las.livro_id = l.id
                GROUP BY ass.id, ass.descricao
                ORDER BY total_livros DESC;
            END
        ");

        DB::unprepared("
            CREATE PROCEDURE sp_ficha_detalhada_livro(IN p_livro_id VARCHAR(36))
            BEGIN
                DECLARE v_livro_id CHAR(36);
                SET v_livro_id = CONVERT(p_livro_id USING utf8mb4) COLLATE utf8mb4_unicode_ci;
                
                SELECT 
                    l.id,
                    l.titulo,
                    l.editora,
                    l.edicao,
                    l.ano_publicacao,
                    l.preco,
                    COALESCE(GROUP_CONCAT(DISTINCT a.nome ORDER BY a.nome SEPARATOR ', '), '') AS autores,
                    COALESCE(GROUP_CONCAT(DISTINCT ass.descricao ORDER BY ass.descricao SEPARATOR ', '), '') AS assuntos,
                    COUNT(DISTINCT a.id) AS total_autores,
                    COUNT(DISTINCT ass.id) AS total_assuntos
                FROM livro l
                LEFT JOIN livro_autor la ON l.id = la.livro_id
                LEFT JOIN autor a ON la.autor_id = a.id
                LEFT JOIN livro_assunto las ON l.id = las.livro_id
                LEFT JOIN assunto ass ON las.assunto_id = ass.id
                WHERE CONVERT(l.id USING utf8mb4) COLLATE utf8mb4_unicode_ci = v_livro_id
                GROUP BY l.id, l.titulo, l.editora, l.edicao, l.ano_publicacao, l.preco;
            END
        ");

        DB::unprepared("
            CREATE PROCEDURE sp_relatorio_por_autor()
            BEGIN
                SELECT 
                    a.id AS autor_id,
                    a.nome AS autor_nome,
                    COUNT(DISTINCT l.id) AS total_livros,
                    GROUP_CONCAT(DISTINCT l.titulo ORDER BY l.titulo SEPARATOR ' | ') AS titulos_livros,
                    GROUP_CONCAT(DISTINCT ass.descricao ORDER BY ass.descricao SEPARATOR ', ') AS categorias,
                    SUM(l.preco) AS valor_total,
                    AVG(l.preco) AS preco_medio,
                    MIN(l.ano_publicacao) AS primeiro_livro_ano,
                    MAX(l.ano_publicacao) AS ultimo_livro_ano
                FROM autor a
                LEFT JOIN livro_autor la ON a.id = la.autor_id
                LEFT JOIN livro l ON la.livro_id = l.id
                LEFT JOIN livro_assunto las ON l.id = las.livro_id
                LEFT JOIN assunto ass ON las.assunto_id = ass.id
                GROUP BY a.id, a.nome
                ORDER BY total_livros DESC, a.nome;
            END
        ");
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            return;
        }
        
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_livros_por_categoria');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_ficha_detalhada_livro');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_relatorio_por_autor');
    }
};
