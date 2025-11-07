<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Entity\Assunto;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Eloquent Repository - AssuntoRepositoryInterface', function () {
    
    test('deve criar um assunto através do repositório', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new Assunto(
            descricao: new Descricao('Romance')
        );

        $assuntoCriado = $repository->create($assunto);

        expect($assuntoCriado->getId())->not()->toBeNull()
            ->and($assuntoCriado->getId()->value())->toBe($assunto->getId()->value())
            ->and($assuntoCriado->getDescricao()->value())->toBe('Romance');
    });

    test('deve encontrar um assunto por ID através do repositório', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new Assunto(
            descricao: new Descricao('Romance')
        );

        $assuntoCriado = $repository->create($assunto);
        $assuntoEncontrado = $repository->findById($assuntoCriado->getId()->value());

        expect($assuntoEncontrado)->not()->toBeNull()
            ->and($assuntoEncontrado->getId()->value())->toBe($assuntoCriado->getId()->value())
            ->and($assuntoEncontrado->getDescricao()->value())->toBe('Romance');
    });

    test('deve retornar null ao buscar assunto inexistente', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assuntoEncontrado = $repository->findById(Uuid::random()->value());

        expect($assuntoEncontrado)->toBeNull();
    });

    test('deve listar todos os assuntos através do repositório', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto1 = new Assunto(descricao: new Descricao('Romance'));
        $assunto2 = new Assunto(descricao: new Descricao('Ficção'));

        $repository->create($assunto1);
        $repository->create($assunto2);

        $assuntos = $repository->findAll();

        expect($assuntos)->toHaveCount(2);
        
        $descricoes = array_map(fn($a) => $a->getDescricao()->value(), $assuntos);
        expect($descricoes)->toContain('Romance')
            ->and($descricoes)->toContain('Ficção');
    });

    test('deve atualizar um assunto através do repositório', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new Assunto(
            descricao: new Descricao('Romance')
        );

        $assuntoCriado = $repository->create($assunto);
        $newAssunto = new Assunto(
            descricao: new Descricao('Romance Atualizado'),
            id: $assuntoCriado->getId()
        );

        $assuntoAtualizado = $repository->update($newAssunto);

        expect($assuntoAtualizado->getDescricao()->value())->toBe('Romance Atualizado')
            ->and($assuntoAtualizado->getId()->value())->toBe($assuntoCriado->getId()->value());
    });

    test('deve deletar um assunto através do repositório', function () {
        $repository = app(AssuntoRepositoryInterface::class);
        
        $assunto = new Assunto(
            descricao: new Descricao('Romance')
        );

        $assuntoCriado = $repository->create($assunto);
        $assuntoId = $assuntoCriado->getId()->value();
        
        $repository->delete($assuntoId);
        
        $assuntoEncontrado = $repository->findById($assuntoId);

        expect($assuntoEncontrado)->toBeNull();
    });

})->group('Eloquent', 'Feature', 'Repository', 'Assunto');
