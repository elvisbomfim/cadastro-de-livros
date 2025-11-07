<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Entity\Autor;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Eloquent Repository - AutorRepositoryInterface', function () {
    
    test('deve criar um autor através do repositório', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new Autor(
            nome: new Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);

        expect($autorCriado->getId())->not()->toBeNull()
            ->and($autorCriado->getId()->value())->toBe($autor->getId()->value())
            ->and($autorCriado->getNome()->value())->toBe('Machado de Assis');
    });

    test('deve encontrar um autor por ID através do repositório', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new Autor(
            nome: new Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $autorEncontrado = $repository->findById($autorCriado->getId()->value());

        expect($autorEncontrado)->not()->toBeNull()
            ->and($autorEncontrado->getId()->value())->toBe($autorCriado->getId()->value())
            ->and($autorEncontrado->getNome()->value())->toBe('Machado de Assis');
    });

    test('deve retornar null ao buscar autor inexistente', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autorEncontrado = $repository->findById(Uuid::random()->value());

        expect($autorEncontrado)->toBeNull();
    });

    test('deve listar todos os autores através do repositório', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        $autor2 = new Autor(nome: new Nome('Clarice Lispector'));

        $repository->create($autor1);
        $repository->create($autor2);

        $autores = $repository->findAll();

        expect($autores)->toHaveCount(2);
        
        $nomes = array_map(fn($a) => $a->getNome()->value(), $autores);
        expect($nomes)->toContain('Machado de Assis')
            ->and($nomes)->toContain('Clarice Lispector');
    });

    test('deve atualizar um autor através do repositório', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new Autor(
            nome: new Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $newAutor = new Autor(
            nome: new Nome('Machado de Assis Atualizado'),
            id: $autorCriado->getId()
        );

        $autorAtualizado = $repository->update($newAutor);

        expect($autorAtualizado->getNome()->value())->toBe('Machado de Assis Atualizado')
            ->and($autorAtualizado->getId()->value())->toBe($autorCriado->getId()->value());
    });

    test('deve deletar um autor através do repositório', function () {
        $repository = app(AutorRepositoryInterface::class);
        
        $autor = new Autor(
            nome: new Nome('Machado de Assis')
        );

        $autorCriado = $repository->create($autor);
        $autorId = $autorCriado->getId()->value();
        
        $repository->delete($autorId);
        
        $autorEncontrado = $repository->findById($autorId);

        expect($autorEncontrado)->toBeNull();
    });

})->group('Eloquent', 'Feature', 'Repository', 'Autor');
