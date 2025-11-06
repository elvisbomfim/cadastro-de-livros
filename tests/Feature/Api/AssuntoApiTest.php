<?php

namespace Tests\Feature\Api;

use Core\Domain\Repository\AssuntoRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('API Assuntos', function () {
    
    test('deve listar todos os assuntos', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        // Criar alguns assuntos
        for ($i = 1; $i <= 3; $i++) {
            $assunto = new \Core\Domain\Entity\Assunto(
                descricao: new \Core\Domain\ValueObject\Descricao("Assunto {$i}")
            );
            $repository->create($assunto);
        }

        $response = $this->getJson('/api/assuntos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'descricao']
                ],
            ])
            ->assertJsonCount(3, 'data');
    });

    test('deve criar um assunto', function () {
        $response = $this->postJson('/api/assuntos', [
            'descricao' => 'Literatura Brasileira',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'descricao']
            ])
            ->assertJson([
                'data' => [
                    'descricao' => 'Literatura Brasileira',
                ],
            ]);

        expect($response->json('data.id'))->not()->toBeNull()
            ->and(strlen($response->json('data.id')))->toBe(36);
    });

    test('deve buscar um assunto por ID', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new \Core\Domain\Entity\Assunto(
            descricao: new \Core\Domain\ValueObject\Descricao('Literatura Brasileira')
        );

        $assuntoCriado = $repository->create($assunto);
        $assuntoId = $assuntoCriado->getId()->value();

        $response = $this->getJson("/api/assuntos/{$assuntoId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'descricao']
            ])
            ->assertJson([
                'data' => [
                    'id' => $assuntoId,
                    'descricao' => 'Literatura Brasileira',
                ],
            ]);
    });

    test('deve retornar 404 ao buscar assunto inexistente', function () {
        $uuidInexistente = \Core\Domain\ValueObject\Uuid::random()->value();

        $response = $this->getJson("/api/assuntos/{$uuidInexistente}");

        $response->assertStatus(404);
    });

    test('deve atualizar um assunto', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new \Core\Domain\Entity\Assunto(
            descricao: new \Core\Domain\ValueObject\Descricao('Literatura Brasileira')
        );

        $assuntoCriado = $repository->create($assunto);
        $assuntoId = $assuntoCriado->getId()->value();

        $response = $this->putJson("/api/assuntos/{$assuntoId}", [
            'descricao' => 'Literatura Brasileira Atualizada',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $assuntoId,
                    'descricao' => 'Literatura Brasileira Atualizada',
                ],
            ]);
    });

    test('deve deletar um assunto', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new \Core\Domain\Entity\Assunto(
            descricao: new \Core\Domain\ValueObject\Descricao('Literatura Brasileira')
        );

        $assuntoCriado = $repository->create($assunto);
        $assuntoId = $assuntoCriado->getId()->value();

        $response = $this->deleteJson("/api/assuntos/{$assuntoId}");

        $response->assertStatus(204);

        // Verificar que o assunto foi deletado
        $responseGet = $this->getJson("/api/assuntos/{$assuntoId}");
        $responseGet->assertStatus(404);
    });

    test('deve retornar lista vazia quando não há assuntos', function () {
        $response = $this->getJson('/api/assuntos');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ])
            ->assertJsonCount(0, 'data');
    });

    test('deve retornar erro de validação ao criar assunto com dados inválidos', function () {
        $response = $this->postJson('/api/assuntos', [
            'descricao' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    });

})->group('API', 'Feature', 'Assunto');
