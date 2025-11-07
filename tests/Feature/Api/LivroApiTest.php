<?php

namespace Tests\Feature\Api;

use App\Models\Autor;
use App\Models\Assunto;
use Core\Domain\Repository\LivroRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('API Livros', function () {
    
    test('deve listar livros paginados', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        // Criar alguns livros
        for ($i = 1; $i <= 5; $i++) {
            $livro = new \Core\Domain\Entity\Livro(
                titulo: new \Core\Domain\ValueObject\Titulo("Livro {$i}"),
                editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
                edicao: new \Core\Domain\ValueObject\Edicao(1),
                anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899 + $i),
                preco: new \Core\Domain\ValueObject\Moeda(35.50 + $i)
            );
            $repository->create($livro);
        }

        $response = $this->getJson('/api/livros');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'titulo',
                        'editora',
                        'edicao',
                        'ano_publicacao',
                        'preco'
                    ]
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'first_page',
                    'from',
                    'to',
                ],
            ])
            ->assertJsonCount(5, 'data');
    });

    test('deve criar um livro', function () {
        $response = $this->postJson('/api/livros', [
            'titulo' => 'Dom Casmurro',
            'editora' => 'Editora Globo',
            'edicao' => 1,
            'ano_publicacao' => 1899,
            'preco' => 35.50,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'titulo', 'editora', 'edicao', 'ano_publicacao', 'preco', 'preco_value']
            ])
            ->assertJson([
                'data' => [
                    'titulo' => 'Dom Casmurro',
                    'editora' => 'Editora Globo',
                    'edicao' => 1,
                    'ano_publicacao' => 1899,
                    'preco' => 'R$ 35,50',
                    'preco_value' => 35.50,
                ],
            ]);

        expect($response->json('data.id'))->not()->toBeNull()
            ->and(strlen($response->json('data.id')))->toBe(36);
    });

    test('deve buscar um livro por ID', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Dom Casmurro'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899),
            preco: new \Core\Domain\ValueObject\Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroId = $livroCriado->getId()->value();

        $response = $this->getJson("/api/livros/{$livroId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'titulo', 'editora', 'edicao', 'ano_publicacao', 'preco', 'preco_value']
            ])
            ->assertJson([
                'data' => [
                    'id' => $livroId,
                    'titulo' => 'Dom Casmurro',
                    'editora' => 'Editora Globo',
                    'edicao' => 1,
                    'ano_publicacao' => 1899,
                    'preco' => 'R$ 35,50',
                    'preco_value' => 35.50,
                ],
            ]);
    });

    test('deve retornar 404 ao buscar livro inexistente', function () {
        $uuidInexistente = \Core\Domain\ValueObject\Uuid::random()->value();

        $response = $this->getJson("/api/livros/{$uuidInexistente}");

        $response->assertStatus(404);
    });

    test('deve atualizar um livro', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Dom Casmurro'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899),
            preco: new \Core\Domain\ValueObject\Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroId = $livroCriado->getId()->value();

        $response = $this->putJson("/api/livros/{$livroId}", [
            'titulo' => 'Dom Casmurro Atualizado',
            'editora' => 'Editora Nova',
            'edicao' => 2,
            'ano_publicacao' => 1900,
            'preco' => 40.00,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $livroId,
                    'titulo' => 'Dom Casmurro Atualizado',
                    'editora' => 'Editora Nova',
                    'edicao' => 2,
                    'ano_publicacao' => 1900,
                    'preco' => 'R$ 40,00',
                    'preco_value' => 40.00,
                ],
            ]);
    });

    test('deve deletar um livro', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Dom Casmurro'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899),
            preco: new \Core\Domain\ValueObject\Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroId = $livroCriado->getId()->value();

        $response = $this->deleteJson("/api/livros/{$livroId}");

        $response->assertStatus(204);

        // Verificar que o livro foi deletado
        $responseGet = $this->getJson("/api/livros/{$livroId}");
        $responseGet->assertStatus(404);
    });

    test('deve filtrar livros por título', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro1 = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Dom Casmurro'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899),
            preco: new \Core\Domain\ValueObject\Moeda(35.50)
        );

        $livro2 = new \Core\Domain\Entity\Livro(
            titulo: new \Core\Domain\ValueObject\Titulo('Memórias Póstumas'),
            editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
            edicao: new \Core\Domain\ValueObject\Edicao(1),
            anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1881),
            preco: new \Core\Domain\ValueObject\Moeda(40.00)
        );

        $repository->create($livro1);
        $repository->create($livro2);

        $response = $this->getJson('/api/livros?filter=Dom');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'titulo' => 'Dom Casmurro',
                    ],
                ],
            ]);
    });

    test('deve paginar livros corretamente', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        // Criar 25 livros
        for ($i = 1; $i <= 25; $i++) {
            $livro = new \Core\Domain\Entity\Livro(
                titulo: new \Core\Domain\ValueObject\Titulo("Livro {$i}"),
                editora: new \Core\Domain\ValueObject\Editora('Editora Globo'),
                edicao: new \Core\Domain\ValueObject\Edicao(1),
                anoPublicacao: new \Core\Domain\ValueObject\AnoPublicacao(1899 + $i),
                preco: new \Core\Domain\ValueObject\Moeda(35.50 + $i)
            );
            $repository->create($livro);
        }

        $response = $this->getJson('/api/livros?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'total' => 25,
                    'per_page' => 10,
                    'current_page' => 1,
                    'last_page' => 3,
                ],
            ])
            ->assertJsonCount(10, 'data');
    });

    test('deve retornar lista vazia quando não há livros', function () {
        $response = $this->getJson('/api/livros');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'per_page' => 15,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ],
            ])
            ->assertJsonCount(0, 'data');
    });

    test('deve retornar erro de validação ao criar livro com dados inválidos', function () {
        $response = $this->postJson('/api/livros', [
            'titulo' => '',
            'editora' => '',
            'edicao' => 0,
            'ano_publicacao' => 0,
            'preco' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    });

})->group('API', 'Feature', 'Livro');
