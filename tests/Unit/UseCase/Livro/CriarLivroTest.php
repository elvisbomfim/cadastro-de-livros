<?php

namespace Tests\Unit\UseCase\Livro;

use Core\UseCase\Livro\CriarLivroUseCase;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Entity\Livro;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Mockery;

describe('Use Case CriarLivro', function () {
    
    test('deve criar um livro com sucesso', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new CriarLivroUseCase($repository);
        
        $livroEsperado = new Livro(
            titulo: new Titulo('O Segredo da Mente Milionária'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2005),
            preco: new Moeda(29.90)
        );
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($livroEsperado);
        
        $livro = $useCase->execute(
            'O Segredo da Mente Milionária',
            'Editora Sextante',
            1,
            2005,
            29.90
        );
        
        expect($livro)->toBeInstanceOf(Livro::class)
            ->and($livro->getTitulo()->value())->toBe('O Segredo da Mente Milionária')
            ->and($livro->getEditora()->value())->toBe('Editora Sextante')
            ->and($livro->getEdicao()->value())->toBe(1)
            ->and($livro->getAnoPublicacao()->value())->toBe(2005)
            ->and($livro->getPreco()->value())->toBe(29.90);
    });

    test('deve criar múltiplos livros diferentes com sucesso', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new CriarLivroUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Zênite'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.00)
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('A Revolta de Atlas'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1957),
            preco: new Moeda(49.90)
        );
        
        $livro3 = new Livro(
            titulo: new Titulo('Pai Rico, Pai Pobre'),
            editora: new Editora('Editora Alta Books'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1997),
            preco: new Moeda(39.99)
        );
        
        $repository->shouldReceive('create')
            ->times(3)
            ->andReturn($livro1, $livro2, $livro3);
        
        $resultado1 = $useCase->execute('Dom Casmurro', 'Editora Zênite', 1, 1899, 35.00);
        $resultado2 = $useCase->execute('A Revolta de Atlas', 'Editora Sextante', 1, 1957, 49.90);
        $resultado3 = $useCase->execute('Pai Rico, Pai Pobre', 'Editora Alta Books', 1, 1997, 39.99);
        
        expect($resultado1->getTitulo()->value())->toBe('Dom Casmurro')
            ->and($resultado2->getTitulo()->value())->toBe('A Revolta de Atlas')
            ->and($resultado3->getTitulo()->value())->toBe('Pai Rico, Pai Pobre')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve chamar o repository create uma vez', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new CriarLivroUseCase($repository);
        
        $livroEsperado = new Livro(
            titulo: new Titulo('Teste'),
            editora: new Editora('Editora Teste'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(25.00)
        );
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($livroEsperado);
        
        $useCase->execute('Teste', 'Editora Teste', 1, 2000, 25.00);
    });

})->group('UseCase', 'Livro');

