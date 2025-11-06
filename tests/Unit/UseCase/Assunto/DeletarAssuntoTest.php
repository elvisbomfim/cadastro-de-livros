<?php

namespace Tests\Unit\UseCase\Assunto;

use Core\UseCase\Assunto\DeletarAssuntoUseCase;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Entity\Assunto;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case DeletarAssunto', function () {
    
    test('deve deletar um assunto existente', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new DeletarAssuntoUseCase($repository);
        
        $assuntoExistente = new Assunto(
            descricao: new Descricao('Assunto para Deletar'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($assuntoExistente);
        
        $repository->shouldReceive('delete')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000');
        
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve deletar múltiplos assuntos diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new DeletarAssuntoUseCase($repository);
        
        $assunto1 = new Assunto(
            descricao: new Descricao('Assunto 1'),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $assunto2 = new Assunto(
            descricao: new Descricao('Assunto 2'),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $assunto3 = new Assunto(
            descricao: new Descricao('Assunto 3'),
            id: new Uuid('33333333-3333-3333-3333-333333333333')
        );
        
        $repository->shouldReceive('findById')
            ->with('11111111-1111-1111-1111-111111111111')
            ->once()
            ->andReturn($assunto1);
        
        $repository->shouldReceive('findById')
            ->with('22222222-2222-2222-2222-222222222222')
            ->once()
            ->andReturn($assunto2);
        
        $repository->shouldReceive('findById')
            ->with('33333333-3333-3333-3333-333333333333')
            ->once()
            ->andReturn($assunto3);
        
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

    test('deve lançar exceção ao tentar deletar assunto inexistente', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new DeletarAssuntoUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class, 'Assunto não encontrado com ID: id-inexistente');

    test('deve lançar exceção com diferentes IDs inexistentes', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new DeletarAssuntoUseCase($repository);
        
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

})->group('UseCase', 'Assunto');

