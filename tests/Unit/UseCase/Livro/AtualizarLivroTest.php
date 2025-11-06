<?php

namespace Tests\Unit\UseCase\Livro;

use Core\UseCase\Livro\AtualizarLivroUseCase;
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

describe('Use Case AtualizarLivro', function () {
    
    test('deve atualizar um livro existente', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new AtualizarLivroUseCase($repository);
        
        $livroExistente = new Livro(
            titulo: new Titulo('Título Antigo'),
            editora: new Editora('Editora Antiga'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(2000),
            preco: new Moeda(25.00),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $livroAtualizado = new Livro(
            titulo: new Titulo('Título Novo'),
            editora: new Editora('Editora Nova'),
            edicao: new Edicao(2),
            anoPublicacao: new AnoPublicacao(2020),
            preco: new Moeda(35.00),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($livroExistente);
        
        $repository->shouldReceive('update')
            ->once()
            ->andReturn($livroAtualizado);
        
        $resultado = $useCase->execute(
            '550e8400-e29b-41d4-a716-446655440000',
            'Título Novo',
            'Editora Nova',
            2,
            2020,
            35.00
        );
        
        expect($resultado)->toBeInstanceOf(Livro::class)
            ->and($resultado->getTitulo()->value())->toBe('Título Novo')
            ->and($resultado->getEditora()->value())->toBe('Editora Nova')
            ->and($resultado->getEdicao()->value())->toBe(2)
            ->and($resultado->getAnoPublicacao()->value())->toBe(2020)
            ->and($resultado->getPreco()->value())->toBe(35.00);
    });

    test('deve atualizar múltiplos livros diferentes com sucesso', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new AtualizarLivroUseCase($repository);
        
        $livro1 = new Livro(
            titulo: new Titulo('Livro 1 Atualizado'),
            editora: new Editora('Editora 1'),
            edicao: new Edicao(2),
            anoPublicacao: new AnoPublicacao(2020),
            preco: new Moeda(29.90),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $livro2 = new Livro(
            titulo: new Titulo('Livro 2 Atualizado'),
            editora: new Editora('Editora 2'),
            edicao: new Edicao(3),
            anoPublicacao: new AnoPublicacao(2021),
            preco: new Moeda(49.99),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $livro3 = new Livro(
            titulo: new Titulo('Livro 3 Atualizado'),
            editora: new Editora('Editora 3'),
            edicao: new Edicao(4),
            anoPublicacao: new AnoPublicacao(2022),
            preco: new Moeda(99.50),
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
        
        $repository->shouldReceive('update')
            ->times(3)
            ->andReturn($livro1, $livro2, $livro3);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111', 'Livro 1 Atualizado', 'Editora 1', 2, 2020, 29.90);
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222', 'Livro 2 Atualizado', 'Editora 2', 3, 2021, 49.99);
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333', 'Livro 3 Atualizado', 'Editora 3', 4, 2022, 99.50);
        
        expect($resultado1->getTitulo()->value())->toBe('Livro 1 Atualizado')
            ->and($resultado2->getTitulo()->value())->toBe('Livro 2 Atualizado')
            ->and($resultado3->getTitulo()->value())->toBe('Livro 3 Atualizado');
    });

    test('deve lançar exceção ao tentar atualizar livro inexistente', function () {
        $repository = Mockery::mock(LivroRepositoryInterface::class);
        $useCase = new AtualizarLivroUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente', 'Título', 'Editora', 1, 2000, 25.00);
    })->throws(LivroNaoEncontradoException::class);

})->group('UseCase', 'Livro');

