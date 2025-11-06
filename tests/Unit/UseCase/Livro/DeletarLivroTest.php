<?php

namespace Tests\Unit\UseCase\Livro;

use Core\UseCase\Livro\DeletarLivroUseCase;
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

describe('Use Case DeletarLivro', function () {
    
    test('deve deletar um livro existente', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new DeletarLivroUseCase($repository);
        
        $livroExistente = new Livro(
            titulo: new Titulo('Livro para Deletar'),
            editora: new Editora('Editora'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(29.90),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($livroExistente);
        
        $repository->shouldReceive('delete')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000');
        
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve deletar múltiplos livros diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new DeletarLivroUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Livro 1'),
            editora: new Editora('Editora 1'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(29.90),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('Livro 2'),
            editora: new Editora('Editora 2'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2001),
            preco: new Moeda(29.90),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $livro3 = new Livro(
            titulo: new Titulo('Livro 3'),
            editora: new Editora('Editora 3'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2002),
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
        
        $repository->shouldReceive('delete')
            ->with('11111111-1111-1111-1111-111111111111')
            ->once();
        
        $repository->shouldReceive('delete')
            ->with('22222222-2222-2222-2222-222222222222')
            ->once();
        
        $repository->shouldReceive('delete')
            ->with('33333333-3333-3333-3333-333333333333')
            ->once();
        
        $useCase->execute('11111111-1111-1111-1111-111111111111');
        $useCase->execute('22222222-2222-2222-2222-222222222222');
        $useCase->execute('33333333-3333-3333-3333-333333333333');
    });

    test('deve chamar findById e delete na sequência correta', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new DeletarLivroUseCase($repository);
        
        $livro = new Livro(
            titulo: new Titulo('Teste'),
            editora: new Editora('Editora'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(29.90),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($livro)
            ->ordered();
        
        $repository->shouldReceive('delete')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->ordered();
        
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve lançar exceção ao tentar deletar livro inexistente', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new DeletarLivroUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class, 'Livro não encontrado com ID: id-inexistente');

    test('deve lançar exceção com diferentes IDs inexistentes', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new DeletarLivroUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->with('id-1')
            ->once()
            ->andReturn(null);
        
        $repository->shouldReceive('findById')
            ->with('id-2')
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
    });

})->group('UseCase', 'Livro');

