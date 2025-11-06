<?php

namespace Tests\Feature\Api;

use Core\Domain\Repository\AutorRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('API Autores', function () {
    
    test('deve listar todos os autores', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        // Criar alguns autores
        for ($i = 1; $i <= 3; $i++) {
            $autor = new \Core\Domain\Entity\Autor(
                nome: new \Core\Domain\ValueObject\Nome("Autor {$i}")
            );
            $repository->create($autor);
        }

        $response = $this->getJson('/api/autores');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nome']
                ],
            ])
            ->assertJsonCount(3, 'data');
    });

    test('deve criar um autor', function () {
        $response = $this->postJson('/api/autores', [
            'nome' => 'Machado de Assis',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'nome']
            ])
            ->assertJson([
                'data' => [
                    'nome' => 'Machado de Assis',
                ],
            ]);

        expect($response->json('data.id'))->not()->toBeNull()
            ->and(strlen($response->json('data.id')))->toBe(36);
    });

    test('deve buscar um autor por ID', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new \Core\Domain\Entity\Autor(
            nome: new \Core\Domain\ValueObject\Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $autorId = $autorCriado->getId()->value();

        $response = $this->getJson("/api/autores/{$autorId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'nome']
            ])
            ->assertJson([
                'data' => [
                    'id' => $autorId,
                    'nome' => 'Machado de Assis',
                ],
            ]);
    });

    test('deve retornar 404 ao buscar autor inexistente', function () {
        $uuidInexistente = \Core\Domain\ValueObject\Uuid::random()->value();

        $response = $this->getJson("/api/autores/{$uuidInexistente}");

        $response->assertStatus(404);
    });

    test('deve atualizar um autor', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new \Core\Domain\Entity\Autor(
            nome: new \Core\Domain\ValueObject\Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $autorId = $autorCriado->getId()->value();

        $response = $this->putJson("/api/autores/{$autorId}", [
            'nome' => 'Machado de Assis Atualizado',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $autorId,
                    'nome' => 'Machado de Assis Atualizado',
                ],
            ]);
    });

    test('deve deletar um autor', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new \Core\Domain\Entity\Autor(
            nome: new \Core\Domain\ValueObject\Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $autorId = $autorCriado->getId()->value();

        $response = $this->deleteJson("/api/autores/{$autorId}");

        $response->assertStatus(204);

        // Verificar que o autor foi deletado
        $responseGet = $this->getJson("/api/autores/{$autorId}");
        $responseGet->assertStatus(404);
    });

    test('deve retornar lista vazia quando não há autores', function () {
        $response = $this->getJson('/api/autores');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ])
            ->assertJsonCount(0, 'data');
    });

    test('deve retornar erro de validação ao criar autor com dados inválidos', function () {
        $response = $this->postJson('/api/autores', [
            'nome' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    });

})->group('API', 'Feature', 'Autor');

