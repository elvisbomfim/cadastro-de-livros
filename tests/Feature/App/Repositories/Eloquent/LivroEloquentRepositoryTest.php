<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Entity\Livro;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Eloquent Repository - LivroRepositoryInterface', function () {
    
    test('deve criar um livro através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);

        expect($livroCriado->getId())->not()->toBeNull()
            ->and($livroCriado->getId()->value())->toBe($livro->getId()->value())
            ->and($livroCriado->getTitulo()->value())->toBe('Dom Casmurro')
            ->and($livroCriado->getEdicao()->value())->toBe(1)
            ->and($livroCriado->getPreco()->value())->toBe(35.50);
    });

    test('deve encontrar um livro por ID através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroEncontrado = $repository->findById($livroCriado->getId()->value());

        expect($livroEncontrado)->not()->toBeNull()
            ->and($livroEncontrado->getId()->value())->toBe($livroCriado->getId()->value())
            ->and($livroEncontrado->getTitulo()->value())->toBe('Dom Casmurro');
    });

    test('deve retornar null ao buscar livro inexistente', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livroEncontrado = $repository->findById(Uuid::random()->value());

        expect($livroEncontrado)->toBeNull();
    });

    test('deve listar todos os livros através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro1 = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $livro2 = new Livro(
            titulo: new Titulo('Memórias Póstumas'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1881),
            preco: new Moeda(40.00)
        );

        $repository->create($livro1);
        $repository->create($livro2);

        $livros = $repository->findAll();

        expect($livros)->toHaveCount(2)
            ->and($livros[0]->getTitulo()->value())->toBe('Dom Casmurro')
            ->and($livros[1]->getTitulo()->value())->toBe('Memórias Póstumas');
    });

    test('deve atualizar um livro através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroCriado->atualizarEdicao(2);

        $livroAtualizado = $repository->update($livroCriado);

        expect($livroAtualizado->getEdicao()->value())->toBe(2);
    });

    test('deve deletar um livro através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $livroCriado = $repository->create($livro);
        $livroId = $livroCriado->getId()->value();
        
        $repository->delete($livroId);
        
        $livroEncontrado = $repository->findById($livroId);

        expect($livroEncontrado)->toBeNull();
    });

})->group('Eloquent', 'Feature', 'Repository', 'Livro');
