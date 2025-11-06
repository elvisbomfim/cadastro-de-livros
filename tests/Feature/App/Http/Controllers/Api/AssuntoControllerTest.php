<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\AssuntoController;
use Core\Domain\Entity\Assunto;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Assunto\CriarAssuntoUseCase;
use Core\UseCase\Assunto\ListarAssuntoUseCase;
use Core\UseCase\Assunto\ListarAssuntosUseCase;
use Core\UseCase\Assunto\AtualizarAssuntoUseCase;
use Core\UseCase\Assunto\DeletarAssuntoUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;

uses(RefreshDatabase::class);

describe('AssuntoController', function () {
    
    beforeEach(function () {
        $this->criarAssuntoUseCase = Mockery::mock(CriarAssuntoUseCase::class);
        $this->listarAssuntoUseCase = Mockery::mock(ListarAssuntoUseCase::class);
        $this->listarAssuntosUseCase = Mockery::mock(ListarAssuntosUseCase::class);
        $this->atualizarAssuntoUseCase = Mockery::mock(AtualizarAssuntoUseCase::class);
        $this->deletarAssuntoUseCase = Mockery::mock(DeletarAssuntoUseCase::class);
        
        $this->controller = new AssuntoController(
            $this->criarAssuntoUseCase,
            $this->listarAssuntoUseCase,
            $this->listarAssuntosUseCase,
            $this->atualizarAssuntoUseCase,
            $this->deletarAssuntoUseCase
        );
    });

    afterEach(function () {
        Mockery::close();
    });

    test('deve listar todos os assuntos', function () {
        $assunto1 = new Assunto(
            descricao: new Descricao('Literatura Brasileira'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $assunto2 = new Assunto(
            descricao: new Descricao('Romance'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174001')
        );

        $this->listarAssuntosUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn([$assunto1, $assunto2]);

        $response = $this->controller->index();

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toHaveCount(2)
            ->and($data['data'][0]['descricao'])->toBe('Literatura Brasileira')
            ->and($data['data'][1]['descricao'])->toBe('Romance');
    });

    test('deve retornar lista vazia quando não há assuntos', function () {
        $this->listarAssuntosUseCase
            ->shouldReceive('execute')
            ->once()
            ->andReturn([]);

        $response = $this->controller->index();

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toBe([])
            ->and(count($data['data']))->toBe(0);
    });

    test('deve criar um assunto', function () {
        $assunto = new Assunto(
            descricao: new Descricao('Literatura Brasileira'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $request = Request::create('/api/assuntos', 'POST', [
            'descricao' => 'Literatura Brasileira',
        ]);

        $this->criarAssuntoUseCase
            ->shouldReceive('execute')
            ->with('Literatura Brasileira')
            ->once()
            ->andReturn($assunto);

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(201);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['descricao'])->toBe('Literatura Brasileira')
            ->and($data['data']['id'])->toBe('123e4567-e89b-12d3-a456-426614174000');
    });

    test('deve retornar erro 422 ao criar assunto com dados inválidos', function () {
        $request = Request::create('/api/assuntos', 'POST', [
            'descricao' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $this->criarAssuntoUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Descrição não pode ter mais de 40 caracteres.'));

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Descrição não pode ter mais de 40 caracteres.');
    });

    test('deve buscar um assunto por ID', function () {
        $assunto = new Assunto(
            descricao: new Descricao('Literatura Brasileira'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarAssuntoUseCase
            ->shouldReceive('execute')
            ->with($assuntoId)
            ->once()
            ->andReturn($assunto);

        $response = $this->controller->show($assuntoId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['id'])->toBe($assuntoId)
            ->and($data['data']['descricao'])->toBe('Literatura Brasileira');
    });

    test('deve retornar 404 ao buscar assunto inexistente', function () {
        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarAssuntoUseCase
            ->shouldReceive('execute')
            ->with($assuntoId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Assunto não encontrado com ID: {$assuntoId}"));

        $response = $this->controller->show($assuntoId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Assunto não encontrado');
    });

    test('deve atualizar um assunto', function () {
        $assunto = new Assunto(
            descricao: new Descricao('Literatura Brasileira Atualizada'),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/assuntos/{$assuntoId}", 'PUT', [
            'descricao' => 'Literatura Brasileira Atualizada',
        ]);

        $this->atualizarAssuntoUseCase
            ->shouldReceive('execute')
            ->with($assuntoId, 'Literatura Brasileira Atualizada')
            ->once()
            ->andReturn($assunto);

        $response = $this->controller->update($request, $assuntoId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['descricao'])->toBe('Literatura Brasileira Atualizada')
            ->and($data['data']['id'])->toBe($assuntoId);
    });

    test('deve retornar 404 ao atualizar assunto inexistente', function () {
        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/assuntos/{$assuntoId}", 'PUT', [
            'descricao' => 'Literatura Brasileira Atualizada',
        ]);

        $this->atualizarAssuntoUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Assunto não encontrado com ID: {$assuntoId}"));

        $response = $this->controller->update($request, $assuntoId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Assunto não encontrado');
    });

    test('deve retornar 422 ao atualizar assunto com dados inválidos', function () {
        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/assuntos/{$assuntoId}", 'PUT', [
            'descricao' => str_repeat('A', 41), // Mais de 40 caracteres
        ]);

        $this->atualizarAssuntoUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Descrição não pode ter mais de 40 caracteres.'));

        $response = $this->controller->update($request, $assuntoId);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Descrição não pode ter mais de 40 caracteres.');
    });

    test('deve deletar um assunto', function () {
        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarAssuntoUseCase
            ->shouldReceive('execute')
            ->with($assuntoId)
            ->once()
            ->andReturnNull();

        $response = $this->controller->destroy($assuntoId);

        expect($response->getStatusCode())->toBe(204);
    });

    test('deve retornar 404 ao deletar assunto inexistente', function () {
        $assuntoId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarAssuntoUseCase
            ->shouldReceive('execute')
            ->with($assuntoId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Assunto não encontrado com ID: {$assuntoId}"));

        $response = $this->controller->destroy($assuntoId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Assunto não encontrado');
    });

})->group('Controller', 'Feature', 'Assunto');

