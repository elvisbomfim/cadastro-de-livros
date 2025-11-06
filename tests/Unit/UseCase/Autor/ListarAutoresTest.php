<?php

namespace Tests\Unit\UseCase\Autor;

use Core\UseCase\Autor\ListarAutoresUseCase;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Entity\Autor;
use Core\Domain\ValueObject\Nome;
use Mockery;

describe('Use Case ListarAutores', function () {
    
    test('deve listar lista vazia quando não há autores', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutoresUseCase($repository);
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([]);
        
        $autores = $useCase->execute();
        
        expect($autores)->toBe([])
            ->and(count($autores))->toBe(0);
    });

    test('deve listar um autor', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutoresUseCase($repository);
        
        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$autor1]);
        
        $autores = $useCase->execute();
        
        expect($autores)->toBeArray()
            ->and(count($autores))->toBe(1)
            ->and($autores[0]->getNome()->value())->toBe('Machado de Assis');
    });

    test('deve listar múltiplos autores diferentes', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutoresUseCase($repository);
        
        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        $autor2 = new Autor(nome: new Nome('Clarice Lispector'));
        $autor3 = new Autor(nome: new Nome('Jorge Amado'));
        
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn([$autor1, $autor2, $autor3]);
        
        $autores = $useCase->execute();
        
        expect($autores)->toBeArray()
            ->and(count($autores))->toBe(3)
            ->and($autores[0]->getNome()->value())->toBe('Machado de Assis')
            ->and($autores[1]->getNome()->value())->toBe('Clarice Lispector')
            ->and($autores[2]->getNome()->value())->toBe('Jorge Amado');
    });

    test('deve retornar array com diferentes tamanhos', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new ListarAutoresUseCase($repository);
        
        $autor1 = new Autor(nome: new Nome('Autor 1'));
        $autor2 = new Autor(nome: new Nome('Autor 2'));
        
        $repository->shouldReceive('findAll')
            ->times(3)
            ->andReturn([], [$autor1], [$autor1, $autor2]);
        
        $resultado1 = $useCase->execute();
        $resultado2 = $useCase->execute();
        $resultado3 = $useCase->execute();
        
        expect(count($resultado1))->toBe(0)
            ->and(count($resultado2))->toBe(1)
            ->and(count($resultado3))->toBe(2);
    });

})->group('UseCase', 'Autor');

