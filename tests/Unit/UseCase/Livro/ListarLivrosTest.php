<?php

namespace Tests\Unit\UseCase\Livro;

use Core\UseCase\Livro\ListarLivrosUseCase;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Entity\Livro;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Mockery;

describe('Use Case ListarLivros', function () {
    
    test('deve listar lista vazia quando não há livros', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivrosUseCase($repository);
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([]);
        
        $livros = $useCase->execute();
        
        expect($livros)->toBe([])
            ->and(count($livros))->toBe(0);
    });

    test('deve listar um livro', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivrosUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('O Segredo da Mente Milionária'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2005),
            preco: new Moeda(29.90)
        );
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$livro1]);
        
        $livros = $useCase->execute();
        
        expect($livros)->toBeArray()
            ->and(count($livros))->toBe(1)
            ->and($livros[0]->getTitulo()->value())->toBe('O Segredo da Mente Milionária');
    });

    test('deve listar múltiplos livros diferentes', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivrosUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Zênite'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(29.90)
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('A Revolta de Atlas'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1957),
            preco: new Moeda(29.90)
        );
        
        $livro3 = new Livro(
            titulo: new Titulo('Pai Rico, Pai Pobre'),
            editora: new Editora('Editora Alta Books'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1997),
            preco: new Moeda(29.90)
        );
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$livro1, $livro2, $livro3]);
        
        $livros = $useCase->execute();
        
        expect($livros)->toBeArray()
            ->and(count($livros))->toBe(3)
            ->and($livros[0]->getTitulo()->value())->toBe('Dom Casmurro')
            ->and($livros[1]->getTitulo()->value())->toBe('A Revolta de Atlas')
            ->and($livros[2]->getTitulo()->value())->toBe('Pai Rico, Pai Pobre');
    });

    test('deve retornar array com diferentes tamanhos', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivrosUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Livro 1'),
            editora: new Editora('Editora 1'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(29.90)
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('Livro 2'),
            editora: new Editora('Editora 2'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2001),
            preco: new Moeda(29.90)
        );
        
        $repository->shouldReceive('findAll')
            ->times(3)
            ->andReturn([], [$livro1], [$livro1, $livro2]);
        
        $resultado1 = $useCase->execute();
        $resultado2 = $useCase->execute();
        $resultado3 = $useCase->execute();
        
        expect(count($resultado1))->toBe(0)
            ->and(count($resultado2))->toBe(1)
            ->and(count($resultado3))->toBe(2);
    });

})->group('UseCase', 'Livro');

