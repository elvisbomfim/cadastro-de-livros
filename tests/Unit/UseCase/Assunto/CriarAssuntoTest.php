<?php

namespace Tests\Unit\UseCase\Assunto;

use Core\UseCase\Assunto\CriarAssuntoUseCase;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Entity\Assunto;
use Core\Domain\ValueObject\Descricao;
use Mockery;

describe('Use Case CriarAssunto', function () {
    
    test('deve criar um assunto com sucesso', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new CriarAssuntoUseCase($repository);
        
        $assuntoEsperado = new Assunto(descricao: new Descricao('Romance'));
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($assuntoEsperado);
        
        $assunto = $useCase->execute('Romance');
        
        expect($assunto)->toBeInstanceOf(Assunto::class)
            ->and($assunto->getDescricao()->value())->toBe('Romance');
    });

    test('deve criar múltiplos assuntos diferentes com sucesso', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new CriarAssuntoUseCase($repository);
        
        $assunto1 = new Assunto(descricao: new Descricao('Romance'));
        $assunto2 = new Assunto(descricao: new Descricao('Ficção Científica'));
        $assunto3 = new Assunto(descricao: new Descricao('Terror'));
        
        $repository->shouldReceive('create')
            ->times(3)
            ->andReturn($assunto1, $assunto2, $assunto3);
        
        $resultado1 = $useCase->execute('Romance');
        $resultado2 = $useCase->execute('Ficção Científica');
        $resultado3 = $useCase->execute('Terror');
        
        expect($resultado1->getDescricao()->value())->toBe('Romance')
            ->and($resultado2->getDescricao()->value())->toBe('Ficção Científica')
            ->and($resultado3->getDescricao()->value())->toBe('Terror')
            ->and($resultado1->id())->not()->toBe($resultado2->id())
            ->and($resultado2->id())->not()->toBe($resultado3->id());
    });

    test('deve chamar o repository create uma vez', function () {
        $repository = Mockery::mock(AssuntoRepositoryInterface::class);
        $useCase = new CriarAssuntoUseCase($repository);
        
        $assuntoEsperado = new Assunto(descricao: new Descricao('Teste'));
        
        $repository->shouldReceive('create')
            ->once()
            ->andReturn($assuntoEsperado);
        
        $useCase->execute('Teste');
    });

})->group('UseCase', 'Assunto');

