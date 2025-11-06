<?php

namespace Tests\Unit\UseCase\Autor;

use Core\UseCase\Autor\CriarAutorUseCase;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Entity\Autor;
use Core\Domain\ValueObject\Nome;
use Mockery;

describe('Use Case CriarAutor', function () {
    
    test('deve criar um autor com sucesso', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new CriarAutorUseCase($repository);
        
        $autorEsperado = new Autor(nome: new Nome('Machado de Assis'));
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($autorEsperado);
        
        $autor = $useCase->execute('Machado de Assis');
        
        expect($autor)->toBeInstanceOf(Autor::class)
            ->and($autor->getNome()->value())->toBe('Machado de Assis');
    });

    test('deve criar múltiplos autores diferentes com sucesso', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new CriarAutorUseCase($repository);
        
        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        $autor2 = new Autor(nome: new Nome('Clarice Lispector'));
        $autor3 = new Autor(nome: new Nome('Jorge Amado'));
        
        $repository->shouldReceive('create')
            ->times(3)
            ->andReturn($autor1, $autor2, $autor3);
        
        $resultado1 = $useCase->execute('Machado de Assis');
        $resultado2 = $useCase->execute('Clarice Lispector');
        $resultado3 = $useCase->execute('Jorge Amado');
        
        expect($resultado1->getNome()->value())->toBe('Machado de Assis')
            ->and($resultado2->getNome()->value())->toBe('Clarice Lispector')
            ->and($resultado3->getNome()->value())->toBe('Jorge Amado')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve chamar o repository create uma vez', function () {
        $repository = Mockery::mock(AutorRepositoryInterface::class);
        $useCase = new CriarAutorUseCase($repository);
        
        $autorEsperado = new Autor(nome: new Nome('Teste'));
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($autorEsperado);
        
        $useCase->execute('Teste');
    });

})->group('UseCase', 'Autor');

