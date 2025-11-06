<?php

namespace Tests\Unit\UseCase\Assunto;

use Core\UseCase\Assunto\ListarAssuntosUseCase;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Entity\Assunto;
use Core\Domain\ValueObject\Descricao;
use Mockery;

describe('Use Case ListarAssuntos', function () {
    
    test('deve listar lista vazia quando não há assuntos', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntosUseCase($repository);
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([]);
        
        $assuntos = $useCase->execute();
        
        expect($assuntos)->toBe([])
            ->and(count($assuntos))->toBe(0);
    });

    test('deve listar um assunto', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntosUseCase($repository);
        
        $assunto1 = new Assunto(descricao: new Descricao('Romance'));
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$assunto1]);
        
        $assuntos = $useCase->execute();
        
        expect($assuntos)->toBeArray()
            ->and(count($assuntos))->toBe(1)
            ->and($assuntos[0]->getDescricao()->value())->toBe('Romance');
    });

    test('deve listar múltiplos assuntos diferentes', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntosUseCase($repository);
        
        $assunto1 = new Assunto(descricao: new Descricao('Romance'));
        $assunto2 = new Assunto(descricao: new Descricao('Ficção Científica'));
        $assunto3 = new Assunto(descricao: new Descricao('Terror'));
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$assunto1, $assunto2, $assunto3]);
        
        $assuntos = $useCase->execute();
        
        expect($assuntos)->toBeArray()
            ->and(count($assuntos))->toBe(3)
            ->and($assuntos[0]->getDescricao()->value())->toBe('Romance')
            ->and($assuntos[1]->getDescricao()->value())->toBe('Ficção Científica')
            ->and($assuntos[2]->getDescricao()->value())->toBe('Terror');
    });

    test('deve retornar array com diferentes tamanhos', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new ListarAssuntosUseCase($repository);
        
        $assunto1 = new Assunto(descricao: new Descricao('Assunto 1'));
        $assunto2 = new Assunto(descricao: new Descricao('Assunto 2'));
        
        $repository->shouldReceive('findAll')
            ->times(3)
            ->andReturn([], [$assunto1], [$assunto1, $assunto2]);
        
        $resultado1 = $useCase->execute();
        $resultado2 = $useCase->execute();
        $resultado3 = $useCase->execute();
        
        expect(count($resultado1))->toBe(0)
            ->and(count($resultado2))->toBe(1)
            ->and(count($resultado3))->toBe(2);
    });

})->group('UseCase', 'Assunto');

