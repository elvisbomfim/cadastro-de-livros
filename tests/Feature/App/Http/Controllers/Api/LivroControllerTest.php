<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\LivroController;
use Core\Domain\Entity\Livro;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Livro\CriarLivroUseCase;
use Core\UseCase\Livro\ListarLivroUseCase;
use Core\UseCase\Livro\ListarLivrosUseCase;
use Core\UseCase\Livro\AtualizarLivroUseCase;
use Core\UseCase\Livro\DeletarLivroUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

describe('LivroController', function () {
    
    beforeEach(function () {
        $this->criarLivroUseCase = Mockery::mock(CriarLivroUseCase::class);
        $this->listarLivroUseCase = Mockery::mock(ListarLivroUseCase::class);
        $this->listarLivrosUseCase = Mockery::mock(ListarLivrosUseCase::class);
        $this->atualizarLivroUseCase = Mockery::mock(AtualizarLivroUseCase::class);
        $this->deletarLivroUseCase = Mockery::mock(DeletarLivroUseCase::class);
        $this->repository = Mockery::mock(LivroRepositoryInterface::class);
        
        $this->controller = new LivroController(
            $this->criarLivroUseCase,
            $this->listarLivroUseCase,
            $this->listarLivrosUseCase,
            $this->atualizarLivroUseCase,
            $this->deletarLivroUseCase,
            $this->repository
        );
    });

    afterEach(function () {
        Mockery::close();
    });

    test('deve listar livros paginados', function () {
        $livro1 = new Livro(
            titulo: new Titulo('Livro 1'),
            editora: new Editora('Editora 1'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2020),
            preco: new Moeda(30.00),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $livro2 = new Livro(
            titulo: new Titulo('Livro 2'),
            editora: new Editora('Editora 2'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2021),
            preco: new Moeda(35.00),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174001')
        );

        $pagination = Mockery::mock(PaginationInterface::class);
        $pagination->shouldReceive('items')->andReturn([$livro1, $livro2]);
        $pagination->shouldReceive('total')->andReturn(2);
        $pagination->shouldReceive('perPage')->andReturn(15);
        $pagination->shouldReceive('currentPage')->andReturn(1);
        $pagination->shouldReceive('lastPage')->andReturn(1);
        $pagination->shouldReceive('firstPage')->andReturn(1);
        $pagination->shouldReceive('from')->andReturn(1);
        $pagination->shouldReceive('to')->andReturn(2);

        $request = Request::create('/api/livros', 'GET', [
            'filter' => '',
            'order' => 'DESC',
            'page' => 1,
            'per_page' => 15,
        ]);

        $this->repository
            ->shouldReceive('paginate')
            ->with('', 'DESC', 1, 15)
            ->once()
            ->andReturn($pagination);

        $response = $this->controller->index($request);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toHaveCount(2)
            ->and($data['meta']['total'])->toBe(2)
            ->and($data['meta']['per_page'])->toBe(15);
    });

    test('deve criar um livro', function () {
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $request = Request::create('/api/livros', 'POST', [
            'titulo' => 'Dom Casmurro',
            'editora' => 'Editora Globo',
            'edicao' => 1,
            'ano_publicacao' => 1899,
            'preco' => 35.50,
        ]);

        $this->criarLivroUseCase
            ->shouldReceive('execute')
            ->with('Dom Casmurro', 'Editora Globo', 1, 1899, 35.50)
            ->once()
            ->andReturn($livro);

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(201);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['titulo'])->toBe('Dom Casmurro')
            ->and($data['data']['editora'])->toBe('Editora Globo')
            ->and($data['data']['edicao'])->toBe(1);
    });

    test('deve retornar erro 422 ao criar livro com dados inválidos', function () {
        $request = Request::create('/api/livros', 'POST', [
            'titulo' => '',
            'editora' => '',
            'edicao' => 0,
            'ano_publicacao' => 0,
            'preco' => 0,
        ]);

        $this->criarLivroUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Título não pode ser vazio.'));

        $response = $this->controller->store($request);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Título não pode ser vazio.');
    });

    test('deve buscar um livro por ID', function () {
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarLivroUseCase
            ->shouldReceive('execute')
            ->with($livroId)
            ->once()
            ->andReturn($livro);

        $response = $this->controller->show($livroId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['id'])->toBe($livroId)
            ->and($data['data']['titulo'])->toBe('Dom Casmurro');
    });

    test('deve retornar 404 ao buscar livro inexistente', function () {
        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $this->listarLivroUseCase
            ->shouldReceive('execute')
            ->with($livroId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Livro não encontrado com ID: {$livroId}"));

        $response = $this->controller->show($livroId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Livro não encontrado');
    });

    test('deve atualizar um livro', function () {
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro Atualizado'),
            editora: new Editora('Editora Nova'),
            edicao: new Edicao(2),
            anoPublicacao: new AnoPublicacao(1900),
            preco: new Moeda(40.00),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/livros/{$livroId}", 'PUT', [
            'titulo' => 'Dom Casmurro Atualizado',
            'editora' => 'Editora Nova',
            'edicao' => 2,
            'ano_publicacao' => 1900,
            'preco' => 40.00,
        ]);

        $this->atualizarLivroUseCase
            ->shouldReceive('execute')
            ->with($livroId, 'Dom Casmurro Atualizado', 'Editora Nova', 2, 1900, 40.00)
            ->once()
            ->andReturn($livro);

        $response = $this->controller->update($request, $livroId);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data']['titulo'])->toBe('Dom Casmurro Atualizado')
            ->and($data['data']['edicao'])->toBe(2);
    });

    test('deve retornar 404 ao atualizar livro inexistente', function () {
        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/livros/{$livroId}", 'PUT', [
            'titulo' => 'Dom Casmurro Atualizado',
            'editora' => 'Editora Nova',
            'edicao' => 2,
            'ano_publicacao' => 1900,
            'preco' => 40.00,
        ]);

        $this->atualizarLivroUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Livro não encontrado com ID: {$livroId}"));

        $response = $this->controller->update($request, $livroId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Livro não encontrado');
    });

    test('deve retornar 422 ao atualizar livro com dados inválidos', function () {
        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $request = Request::create("/api/livros/{$livroId}", 'PUT', [
            'titulo' => '',
            'editora' => '',
            'edicao' => 0,
            'ano_publicacao' => 0,
            'preco' => 0,
        ]);

        $this->atualizarLivroUseCase
            ->shouldReceive('execute')
            ->once()
            ->andThrow(new InvalidArgumentException('Título não pode ser vazio.'));

        $response = $this->controller->update($request, $livroId);

        expect($response->getStatusCode())->toBe(422);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toBe('Título não pode ser vazio.');
    });

    test('deve deletar um livro', function () {
        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarLivroUseCase
            ->shouldReceive('execute')
            ->with($livroId)
            ->once()
            ->andReturnNull();

        $response = $this->controller->destroy($livroId);

        expect($response->getStatusCode())->toBe(204);
    });

    test('deve retornar 404 ao deletar livro inexistente', function () {
        $livroId = '123e4567-e89b-12d3-a456-426614174000';

        $this->deletarLivroUseCase
            ->shouldReceive('execute')
            ->with($livroId)
            ->once()
            ->andThrow(new LivroNaoEncontradoException("Livro não encontrado com ID: {$livroId}"));

        $response = $this->controller->destroy($livroId);

        expect($response->getStatusCode())->toBe(404);
        $data = json_decode($response->getContent(), true);
        expect($data['message'])->toContain('Livro não encontrado');
    });

    test('deve filtrar livros por título na paginação', function () {
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50),
            id: new Uuid('123e4567-e89b-12d3-a456-426614174000')
        );

        $pagination = Mockery::mock(PaginationInterface::class);
        $pagination->shouldReceive('items')->andReturn([$livro]);
        $pagination->shouldReceive('total')->andReturn(1);
        $pagination->shouldReceive('perPage')->andReturn(15);
        $pagination->shouldReceive('currentPage')->andReturn(1);
        $pagination->shouldReceive('lastPage')->andReturn(1);
        $pagination->shouldReceive('firstPage')->andReturn(1);
        $pagination->shouldReceive('from')->andReturn(1);
        $pagination->shouldReceive('to')->andReturn(1);

        $request = Request::create('/api/livros', 'GET', [
            'filter' => 'Dom',
            'order' => 'DESC',
            'page' => 1,
            'per_page' => 15,
        ]);

        $this->repository
            ->shouldReceive('paginate')
            ->with('Dom', 'DESC', 1, 15)
            ->once()
            ->andReturn($pagination);

        $response = $this->controller->index($request);

        expect($response->getStatusCode())->toBe(200);
        $data = json_decode($response->getContent(), true);
        expect($data['data'])->toHaveCount(1)
            ->and($data['meta']['total'])->toBe(1);
    });

})->group('Controller', 'Feature', 'Livro');

