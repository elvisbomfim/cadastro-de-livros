<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        
        // SQLite não suporta views complexas com GROUP_CONCAT ORDER BY
        // Esta view é apenas para MySQL, então pulamos nos testes
        if ($driver === 'sqlite') {
            return;
        }
        
        DB::statement('DROP VIEW IF EXISTS view_livros_por_autor');
        
        DB::statement("
            CREATE VIEW view_livros_por_autor AS
            SELECT 
                a.id AS autor_id,
                a.nome AS autor_nome,
                COUNT(DISTINCT l.id) AS total_livros,
                COUNT(DISTINCT ass.id) AS total_assuntos_distintos,
                GROUP_CONCAT(DISTINCT l.titulo ORDER BY l.titulo SEPARATOR ', ') AS livros_titulos,
                GROUP_CONCAT(DISTINCT ass.descricao ORDER BY ass.descricao SEPARATOR ', ') AS assuntos_descricoes,
                SUM(l.preco) AS valor_total_livros,
                AVG(l.preco) AS preco_medio_livros,
                MIN(l.ano_publicacao) AS ano_publicacao_mais_antigo,
                MAX(l.ano_publicacao) AS ano_publicacao_mais_recente
            FROM autor a
            LEFT JOIN livro_autor la ON a.id = la.autor_id
            LEFT JOIN livro l ON la.livro_id = l.id
            LEFT JOIN livro_assunto las ON l.id = las.livro_id
            LEFT JOIN assunto ass ON las.assunto_id = ass.id
            GROUP BY a.id, a.nome
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_livros_por_autor');
    }
};
