<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Autor;
use Core\Domain\ValueObject\Nome;
use Core\Domain\Exception\EntityValidationException;

describe('Entidade Autor', function () {
    
    beforeEach(function () {
        $this->nomeValido1 = new Nome('Machado de Assis');
        $this->nomeValido2 = new Nome('Clarice Lispector');
        $this->nomeValido3 = new Nome('Jorge Amado');
    });

    test('deve criar um Autor e gerar um ID se não for fornecido', function () {
        $autor = new Autor(
            nome: $this->nomeValido1
        );

        expect($autor)->toBeInstanceOf(Autor::class)    
            ->and($autor->id())->not()->toBeNull() 
            ->and($autor->getNome()->value())->toBe('Machado de Assis');
    });

    test('deve criar um Autor com nome diferente e gerar ID único', function () {
        $autor1 = new Autor(
            nome: $this->nomeValido1
        );

        $autor2 = new Autor(
            nome: $this->nomeValido2
        );

        expect($autor1->id())->not()->toBe($autor2->id())
            ->and($autor1->getNome()->value())->toBe('Machado de Assis')
            ->and($autor2->getNome()->value())->toBe('Clarice Lispector');
    });

    test('deve criar múltiplos Autores com diferentes nomes', function () {
        $autor1 = new Autor(nome: $this->nomeValido1);
        $autor2 = new Autor(nome: $this->nomeValido2);
        $autor3 = new Autor(nome: $this->nomeValido3);

        expect($autor1->getNome()->value())->toBe('Machado de Assis')
            ->and($autor2->getNome()->value())->toBe('Clarice Lispector')
            ->and($autor3->getNome()->value())->toBe('Jorge Amado')
            ->and($autor1->id())->not()->toBe($autor2->id())
            ->and($autor2->id())->not()->toBe($autor3->id())
            ->and($autor1->id())->not()->toBe($autor3->id());
    });

    test('deve validar que o nome não pode estar vazio', function () {
        $nomeVazio = new Nome('');
        
        new Autor(nome: $nomeVazio);
    })->throws(\InvalidArgumentException::class);

    test('deve validar que o nome não pode exceder 40 caracteres', function () {
        $nomeLongo = new Nome(str_repeat('A', 41));
        
        new Autor(nome: $nomeLongo);
    })->throws(\InvalidArgumentException::class);

    test('deve aceitar nome com exatamente 40 caracteres', function () {
        $nomeLimite = new Nome(str_repeat('A', 40));
        $autor = new Autor(nome: $nomeLimite);

        expect($autor->getNome()->value())->toBe(str_repeat('A', 40));
    });

    test('deve aceitar nome com 1 caractere', function () {
        $nomeMinimo = new Nome('A');
        $autor = new Autor(nome: $nomeMinimo);

        expect($autor->getNome()->value())->toBe('A');
    });

    test('deve aceitar nome com 39 caracteres', function () {
        $nomeValido = new Nome(str_repeat('B', 39)); 
        $autor = new Autor(nome: $nomeValido);

        expect($autor->getNome()->value())->toBe(str_repeat('B', 39));
    });

    test('deve criar autores com nomes de tamanhos diferentes', function () {
        $autor1 = new Autor(nome: new Nome('A'));
        $autor2 = new Autor(nome: new Nome('João Silva'));
        $autor3 = new Autor(nome: new Nome(str_repeat('C', 40)));

        expect(strlen($autor1->getNome()->value()))->toBe(1)
            ->and(strlen($autor2->getNome()->value()))->toBe(11)
            ->and(strlen($autor3->getNome()->value()))->toBe(40);
    });

    test('deve criar autores com nomes que têm caracteres especiais', function () {
        $autor1 = new Autor(nome: new Nome('José da Silva'));
        $autor2 = new Autor(nome: new Nome('Maria José'));
        $autor3 = new Autor(nome: new Nome('Paulo Coelho'));

        expect($autor1->getNome()->value())->toBe('José da Silva')
            ->and($autor2->getNome()->value())->toBe('Maria José')
            ->and($autor3->getNome()->value())->toBe('Paulo Coelho');
    });

})->group('Entity', 'Domain', 'Autor');

