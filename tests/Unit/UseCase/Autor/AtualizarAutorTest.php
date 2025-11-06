<?php

namespace Tests\Unit\UseCase\Autor;

use Core\UseCase\Autor\AtualizarAutorUseCase;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Entity\Autor;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case AtualizarAutor', function () {
    
    test('deve atualizar um autor existente', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new AtualizarAutorUseCase($repository);
        
        $autorExistente = new Autor(
            nome: new Nome('Nome Antigo'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $autorAtualizado = new Autor(
            nome: new Nome('Nome Novo'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($autorExistente);
        
        $repository->shouldReceive('update')
            ->once()
            ->andReturn($autorAtualizado);
        
        $resultado = $useCase->execute('550e8400-e29b-41d4-a716-446655440000', 'Nome Novo');
        
        expect($resultado)->toBeInstanceOf(Autor::class)
            ->and($resultado->getNome()->value())->toBe('Nome Novo');
    });

    test('deve atualizar múltiplos autores diferentes com sucesso', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new AtualizarAutorUseCase($repository);
        
        $autor1 = new Autor(
            nome: new Nome('Autor 1 Atualizado'),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $autor2 = new Autor(
            nome: new Nome('Autor 2 Atualizado'),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $autor3 = new Autor(
            nome: new Nome('Autor 3 Atualizado'),
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
        
        $repository->shouldReceive('update')
            ->times(3)
            ->andReturn($autor1, $autor2, $autor3);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111', 'Autor 1 Atualizado');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222', 'Autor 2 Atualizado');
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333', 'Autor 3 Atualizado');
        
        expect($resultado1->getNome()->value())->toBe('Autor 1 Atualizado')
            ->and($resultado2->getNome()->value())->toBe('Autor 2 Atualizado')
            ->and($resultado3->getNome()->value())->toBe('Autor 3 Atualizado');
    });

    test('deve lançar exceção ao tentar atualizar autor inexistente', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new AtualizarAutorUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente', 'Nome');
    })->throws(LivroNaoEncontradoException::class);

})->group('UseCase', 'Autor');

