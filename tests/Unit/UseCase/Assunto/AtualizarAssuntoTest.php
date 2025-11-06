<?php

namespace Tests\Unit\UseCase\Assunto;

use Core\UseCase\Assunto\AtualizarAssuntoUseCase;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Entity\Assunto;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;
use Mockery;

describe('Use Case AtualizarAssunto', function () {
    
    test('deve atualizar um assunto existente', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new AtualizarAssuntoUseCase($repository);
        
        $assuntoExistente = new Assunto(
            descricao: new Descricao('Descrição Antiga'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $assuntoAtualizado = new Assunto(
            descricao: new Descricao('Descrição Nova'),
            id: new Uuid('550e8400-e29b-41d4-a716-446655440000')
        );
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($assuntoExistente);
        
        $repository->shouldReceive('update')
            ->once()
            ->andReturn($assuntoAtualizado);
        
        $resultado = $useCase->execute('550e8400-e29b-41d4-a716-446655440000', 'Descrição Nova');
        
        expect($resultado)->toBeInstanceOf(Assunto::class)
            ->and($resultado->getDescricao()->value())->toBe('Descrição Nova');
    });

    test('deve atualizar múltiplos assuntos diferentes com sucesso', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new AtualizarAssuntoUseCase($repository);
        
        $assunto1 = new Assunto(
            descricao: new Descricao('Assunto 1 Atualizado'),
            id: new Uuid('11111111-1111-1111-1111-111111111111')
        );
        
        $assunto2 = new Assunto(
            descricao: new Descricao('Assunto 2 Atualizado'),
            id: new Uuid('22222222-2222-2222-2222-222222222222')
        );
        
        $assunto3 = new Assunto(
            descricao: new Descricao('Assunto 3 Atualizado'),
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
        
        $repository->shouldReceive('update')
            ->times(3)
            ->andReturn($assunto1, $assunto2, $assunto3);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111', 'Assunto 1 Atualizado');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222', 'Assunto 2 Atualizado');
        $resultado3 = $useCase->execute('33333333-3333-3333-3333-333333333333', 'Assunto 3 Atualizado');
        
        expect($resultado1->getDescricao()->value())->toBe('Assunto 1 Atualizado')
            ->and($resultado2->getDescricao()->value())->toBe('Assunto 2 Atualizado')
            ->and($resultado3->getDescricao()->value())->toBe('Assunto 3 Atualizado');
    });

    test('deve lançar exceção ao tentar atualizar assunto inexistente', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new AtualizarAssuntoUseCase($repository);
        
        $repository->shouldReceive('findById')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente', 'Descrição');
    })->throws(LivroNaoEncontradoException::class);

})->group('UseCase', 'Assunto');

