<?php

namespace Tests\Feature\Relatorio;

use App\Models\Autor;
use App\Models\Assunto;
use App\Models\Livro;
use Core\Domain\Repository\LivroRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Relatórios PDF', function () {
    
    beforeEach(function () {
        // Criar dados de teste
        $this->autor = Autor::factory()->create();
        $this->assunto = Assunto::factory()->create();
        
        $repository = app(LivroRepositoryInterface::class);
        $this->livro = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Livro Teste'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Teste'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(2020),
            preco: new \Core\Domain\ValueObject\Moeda(50.00)
        );
        $this->livroCriado = $repository->create($this->livro);
        
        // Associar relacionamentos
        $livroModel = Livro::find($this->livroCriado->getId()->value());
        $livroModel->autors()->attach($this->autor->id);
        $livroModel->assuntos()->attach($this->assunto->id);
    });

    test('deve baixar PDF de livros por categoria', function () {
        // Criar alguns assuntos e livros para ter dados no relatório
        $assunto1 = Assunto::factory()->create(['descricao' => 'Romance']);
        $assunto2 = Assunto::factory()->create(['descricao' => 'Ficção']);
        
        $repository = app(LivroRepositoryInterface::class);
        $livro1 = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Livro 1'),
            editora: new \Core\Domain\ValueObject\Editora('Editora 1'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(2020),
            preco: new \Core\Domain\ValueObject\Moeda(50.00)
        );
        $livro1Criado = $repository->create($livro1);
        $livroModel1 = Livro::find($livro1Criado->getId()->value());
        $livroModel1->assuntos()->attach($assunto1->id);
        
        $response = $this->get('/relatorios/livros-por-categoria/pdf');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        expect($contentDisposition)
            ->toContain('attachment')
            ->toContain('livros-por-categoria.pdf');
    });

    test('deve baixar PDF de ficha detalhada do livro', function () {
        $livroId = $this->livroCriado->getId()->value();
        
        $response = $this->get("/relatorios/livro/{$livroId}/ficha/pdf");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        expect($contentDisposition)
            ->toContain('attachment')
            ->toContain("ficha-livro-{$livroId}.pdf");
    });

    test('deve retornar 404 ao tentar baixar ficha de livro inexistente', function () {
        $livroIdInexistente = '00000000-0000-0000-0000-000000000000';
        
        $response = $this->get("/relatorios/livro/{$livroIdInexistente}/ficha/pdf");

        $response->assertStatus(404);
    });

    test('deve baixar PDF de relatório por autor', function () {
        $response = $this->get('/relatorios/por-autor/pdf');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        expect($contentDisposition)
            ->toContain('attachment')
            ->toContain('relatorio-por-autor.pdf');
    });

    test('deve retornar conteúdo PDF válido para livros por categoria', function () {
        $response = $this->get('/relatorios/livros-por-categoria/pdf');

        $response->assertStatus(200);
        
        $content = $response->getContent();
        expect($content)->not()->toBeEmpty()
            ->and(substr($content, 0, 4))->toBe('%PDF'); // Verifica se é um PDF válido
    });

    test('deve retornar conteúdo PDF válido para ficha detalhada', function () {
        $livroId = $this->livroCriado->getId()->value();
        
        $response = $this->get("/relatorios/livro/{$livroId}/ficha/pdf");

        $response->assertStatus(200);
        
        $content = $response->getContent();
        expect($content)->not()->toBeEmpty()
            ->and(substr($content, 0, 4))->toBe('%PDF'); // Verifica se é um PDF válido
    });

    test('deve retornar conteúdo PDF válido para relatório por autor', function () {
        $response = $this->get('/relatorios/por-autor/pdf');

        $response->assertStatus(200);
        
        $content = $response->getContent();
        expect($content)->not()->toBeEmpty()
            ->and(substr($content, 0, 4))->toBe('%PDF'); // Verifica se é um PDF válido
    });

})->group('Relatorio', 'Feature', 'PDF');

