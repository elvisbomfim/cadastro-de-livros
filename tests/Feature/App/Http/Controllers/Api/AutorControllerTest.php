<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\AutorController;
use Core\Domain\Entity\Autor;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Autor\CriarAutorUseCase;
use Core\UseCase\Autor\ListarAutorUseCase;
use Core\UseCase\Autor\ListarAutoresUseCase;
use Core\UseCase\Autor\AtualizarAutorUseCase;
use Core\UseCase\Autor\DeletarAutorUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;

uses(RefreshDatabase::class);

describe('AutorController', function () {
    
    beforeEach(function () {
        $this->criarAutorUseCase = Mockery::mock(CriarAutorUseCase::class);
        $this->listarAutorUseCase = Mockery::mock(ListarAutorUseCase::class);
        $this->listarAutoresUseCase = Mockery::mock(ListarAutoresUseCase::class);
        $this->atualizarAutorUseCase = Mockery::mock(AtualizarAutorUseCase::class);
        $this->deletarAutorUseCase = Mockery::mock(DeletarAutorUseCase::class);
        
        $this->controller = new AutorController(
            $this->criarAutorUseCase,
            $this->listarAutorUseCase,
            $this->listarAutoresUseCase,
            $this->atualizarAutorUseCase,
            $this->deletarAutorUseCase
        );
    });

    afterEach(function () {
        Mockery::close();
    });

    test('deve listar todos os autores', function () {
        $autor1 = new Autor(
            nome: new Nome('Machado de Assis'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $autor2 = new Autor(
            nome: new Nome('Clarice Lispector'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174001')
        );

        $this->listarAutoresUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn([$autor1, $autor2]);

        $response = $this->controller->index();

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toHaveCount(2)
            ->and($data['data'][0]['nome'])->toBe('Machado de Assis')
            ->and($data['data'][1]['nome'])->toBe('Clarice Lispector');
    });

    test('deve retornar lista vazia quando não há autores', function () {
        $this->listarAutoresUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn([]);

        $response = $this->controller->index();

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toBe([])
            ->and(count($data['data']))->toBe(0);
    });

    test('deve criar um autor', function () {
        $autor = new Autor(
            nome: new Nome('Machado de Assis'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $request = Request::create('/api/autores', 'POST', [
            'nome' => 'Machado de Assis',
        ]);

        $this->criarAutorUseCase
            ->shouldReceive('execute')
            ->with('Machado de Assis')
            ->once()
            ->andReturn($autor);

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(201);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['nome'])->toBe('Machado de Assis')
            ->and($data['data']['id'])->toBe('123e4567-e89b-12d3-a456-426614174000');
    });

    test('deve retornar erro 422 ao criar autor com dados inválidos', function () {
        $request = Request::create('/api/autores', 'POST', [
            'nome' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $this->criarAutorUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Nome não pode ter mais de 40 caracteres.'));

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Nome não pode ter mais de 40 caracteres.');
    });

    test('deve buscar um autor por ID', function () {
        $autor = new Autor(
            nome: new Nome('Machado de Assis'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarAutorUseCase
            ->shouldReceive('execute')
            ->with($autorId)
            ->once()
            ->andReturn($autor);

        $response = $this->controller->show($autorId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['id'])->toBe($autorId)
            ->and($data['data']['nome'])->toBe('Machado de Assis');
    });

    test('deve retornar 404 ao buscar autor inexistente', function () {
        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarAutorUseCase
            ->shouldReceive('execute')
            ->with($autorId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Autor não encontrado com ID: {$autorId}"));

        $response = $this->controller->show($autorId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Autor não encontrado');
    });

    test('deve atualizar um autor', function () {
        $autor = new Autor(
            nome: new Nome('Machado de Assis Atualizado'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/autores/{$autorId}", 'PUT', [
            'nome' => 'Machado de Assis Atualizado',
        ]);

        $this->atualizarAutorUseCase
            ->shouldReceive('execute')
            ->with($autorId, 'Machado de Assis Atualizado')
            ->once()
            ->andReturn($autor);

        $response = $this->controller->update($request, $autorId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['nome'])->toBe('Machado de Assis Atualizado')
            ->and($data['data']['id'])->toBe($autorId);
    });

    test('deve retornar 404 ao atualizar autor inexistente', function () {
        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/autores/{$autorId}", 'PUT', [
            'nome' => 'Machado de Assis Atualizado',
        ]);

        $this->atualizarAutorUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Autor não encontrado com ID: {$autorId}"));

        $response = $this->controller->update($request, $autorId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Autor não encontrado');
    });

    test('deve retornar 422 ao atualizar autor com dados inválidos', function () {
        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/autores/{$autorId}", 'PUT', [
            'nome' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $this->atualizarAutorUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Nome não pode ter mais de 40 caracteres.'));

        $response = $this->controller->update($request, $autorId);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Nome não pode ter mais de 40 caracteres.');
    });

    test('deve deletar um autor', function () {
        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarAutorUseCase
            ->shouldReceive('execute')
            ->with($autorId)
            ->once()
            ->andReturnNull();

        $response = $this->controller->destroy($autorId);

        expect($response->getStatusCode())->toBe(204);
    });

    test('deve retornar 404 ao deletar autor inexistente', function () {
        $autorId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarAutorUseCase
            ->shouldReceive('execute')
            ->with($autorId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Autor não encontrado com ID: {$autorId}"));

        $response = $this->controller->destroy($autorId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Autor não encontrado');
    });

})->group('Controller', 'Feature', 'Autor');

