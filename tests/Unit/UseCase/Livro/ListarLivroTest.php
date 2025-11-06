<?php

namespace Tests\Unit\UseCase\Livro;

use Core\UseCase\Livro\ListarLivroUseCase;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Entity\Livro;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case ListarLivro', function () {
    
    test('deve listar um livro existente', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivroUseCase($repository);
        
        $livroEsperado = new Livro(
            titulo: new Titulo('O Segredo da Mente Milionária'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2005),
            preco: new Moeda(29.90),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($livroEsperado);
        
        $livro = $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
        
        expect($livro)->toBeInstanceOf(Livro::class)
            ->and($livro->getTitulo()->value())->toBe('O Segredo da Mente Milionária')
            ->and($livro->id())->toBe('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve listar múltiplos livros diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivroUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Zênite'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(29.90),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('A Revolta de Atlas'),
            editora: new Editora('Editora Sextante'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1957),
            preco: new Moeda(29.90),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $livro3 = new Livro(
            titulo: new Titulo('Pai Rico, Pai Pobre'),
            editora: new Editora('Editora Alta Books'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1997),
            preco: new Moeda(29.90),
            id: new Uuid('33333333-3333-3333-3333-333333333333')
        );
        
        $repository->shouldReceive('findById')
            ->with('11111111-1111-1111-1111-111111111111')
            ->once()
            ->andReturn($livro1);
        
        $repository->shouldReceive('findById')
            ->with('22222222-2222-2222-2222-222222222222')
            ->once()
            ->andReturn($livro2);
        
        $repository->shouldReceive('findById')
            ->with('33333333-3333-3333-3333-333333333333')
            ->once()
            ->andReturn($livro3);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222');
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333');
        
        expect($resultado1->getTitulo()->value())->toBe('Dom Casmurro')
            ->and($resultado2->getTitulo()->value())->toBe('A Revolta de Atlas')
            ->and($resultado3->getTitulo()->value())->toBe('Pai Rico, Pai Pobre')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve lançar exceção quando livro não encontrado', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivroUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class, 'Livro não encontrado com ID: id-inexistente');

    test('deve lançar exceção com diferentes IDs inexistentes', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new ListarLivroUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->with('id-1')
            ->once()
            ->andReturn(null);
        
        $repository->shouldReceive('findById')
            ->with('id-2')
            ->once()
            ->andReturn(null);
        
        $repository->shouldReceive('findById')
            ->with('id-3')
            ->once()
            ->andReturn(null);
        
        try {
            $useCase->execute('id-1');
        } catch (LivroNaoEncontradoException $e) {
            expect($e->getMessage())->toContain('id-1');
        }
        
        try {
            $useCase->execute('id-2');
        } catch (LivroNaoEncontradoException $e) {
            expect($e->getMessage())->toContain('id-2');
        }
        
        try {
            $useCase->execute('id-3');
        } catch (LivroNaoEncontradoException $e) {
            expect($e->getMessage())->toContain('id-3');
        }
    });

})->group('UseCase', 'Livro');

