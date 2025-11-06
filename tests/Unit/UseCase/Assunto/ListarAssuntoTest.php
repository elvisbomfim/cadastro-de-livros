<?php

namespace Tests\Unit\UseCase\Assunto;

use Core\UseCase\Assunto\ListarAssuntoUseCase;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Entity\Assunto;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case ListarAssunto', function () {
    
    test('deve listar um assunto existente', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntoUseCase($repository);
        
        $assuntoEsperado = new Assunto(
            descricao: new Descricao('Romance'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($assuntoEsperado);
        
        $assunto = $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
        
        expect($assunto)->toBeInstanceOf(Assunto::class)
            ->and($assunto->getDescricao()->value())->toBe('Romance')
            ->and($assunto->id())->toBe('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve listar múltiplos assuntos diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntoUseCase($repository);
        
        $assunto1 = new Assunto(
            descricao: new Descricao('Romance'),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $assunto2 = new Assunto(
            descricao: new Descricao('Ficção Científica'),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $assunto3 = new Assunto(
            descricao: new Descricao('Terror'),
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
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222');
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333');
        
        expect($resultado1->getDescricao()->value())->toBe('Romance')
            ->and($resultado2->getDescricao()->value())->toBe('Ficção Científica')
            ->and($resultado3->getDescricao()->value())->toBe('Terror')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve lançar exceção quando assunto não encontrado', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntoUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class, 'Assunto não encontrado com ID: id-inexistente');

    test('deve lançar exceção com diferentes IDs inexistentes', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntoUseCase($repository);
        
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

