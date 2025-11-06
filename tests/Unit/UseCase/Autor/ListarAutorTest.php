<?php

namespace Tests\Unit\UseCase\Autor;

use Core\UseCase\Autor\ListarAutorUseCase;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Entity\Autor;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case ListarAutor', function () {
    
    test('deve listar um autor existente', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutorUseCase($repository);
        
        $autorEsperado = new Autor(
            nome: new Nome('Machado de Assis'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($autorEsperado);
        
        $autor = $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
        
        expect($autor)->toBeInstanceOf(Autor::class)
            ->and($autor->getNome()->value())->toBe('Machado de Assis')
            ->and($autor->id())->toBe('550e8400-e29b-41d4-a716-446655440000');
    });

    test('deve listar múltiplos autores diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutorUseCase($repository);
        
        $autor1 = new Autor(
            nome: new Nome('Machado de Assis'),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $autor2 = new Autor(
            nome: new Nome('Clarice Lispector'),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $autor3 = new Autor(
            nome: new Nome('Jorge Amado'),
            id: new Uuid('33333333-3333-3333-3333-333333333333')
        );
        
        $repository->shouldReceive('findById')
            ->with('11111111-1111-1111-1111-111111111111')
            ->once()
            ->andReturn($autor1);
        
        $repository->shouldReceive('findById')
            ->with('22222222-2222-2222-2222-222222222222')
            ->once()
            ->andReturn($autor2);
        
        $repository->shouldReceive('findById')
            ->with('33333333-3333-3333-3333-333333333333')
            ->once()
            ->andReturn($autor3);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222');
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333');
        
        expect($resultado1->getNome()->value())->toBe('Machado de Assis')
            ->and($resultado2->getNome()->value())->toBe('Clarice Lispector')
            ->and($resultado3->getNome()->value())->toBe('Jorge Amado')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve lançar exceção quando autor não encontrado', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutorUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class, 'Autor não encontrado com ID: id-inexistente');

    test('deve lançar exceção com diferentes IDs inexistentes', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutorUseCase($repository);
        
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

})->group('UseCase', 'Autor');

