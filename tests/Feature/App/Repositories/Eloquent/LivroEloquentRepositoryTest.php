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

    test('deve paginar livros através do repositório', function () {
        $repository = app(LivroRepositoryInterface::class);
        
        // Criar múltiplos livros
        for ($i = 1; $i <= 20; $i++) {
            $livro = new Livro(
                titulo: new Titulo("Livro {$i}"),
                editora: new Editora('Editora Globo'),
                edicao: new Edicao(1),
                anoPublicacao: new AnoPublicacao(1899 + $i),
                preco: new Moeda(35.50 + $i)
            );
            $repository->create($livro);
        }

        $pagination = $repository->paginate('', 'DESC', 1, 10);

        expect($pagination->total())->toBe(20)
            ->and($pagination->perPage())->toBe(10)
            ->and($pagination->currentPage())->toBe(1)
            ->and($pagination->lastPage())->toBe(2)
            ->and($pagination->firstPage())->toBe(1)
            ->and(count($pagination->items()))->toBe(10);
    });

    test('deve filtrar livros na paginação', function () {
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

        $pagination = $repository->paginate('Dom', 'DESC', 1, 15);

        expect($pagination->total())->toBe(1)
            ->and(count($pagination->items()))->toBe(1);
    });

    test('deve retornar paginação vazia quando não há livros', function () {
        $repository = app(LivroRepositoryInterface::class);

        $pagination = $repository->paginate('', 'DESC', 1, 15);

        expect($pagination->total())->toBe(0)
            ->and($pagination->perPage())->toBe(15)
            ->and($pagination->currentPage())->toBe(1)
            ->and($pagination->lastPage())->toBe(1)
            ->and($pagination->firstPage())->toBe(1)
            ->and($pagination->from())->toBe(0)
            ->and($pagination->to())->toBe(0)
            ->and(count($pagination->items()))->toBe(0);
    });

})->group('Eloquent', 'Feature', 'Repository', 'Livro');
