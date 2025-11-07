<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Assunto;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\Exception\EntityValidationException;

describe('Entidade Assunto', function () {
    
    beforeEach(function () {
        $this->descricaoValida1 = new Descricao('Romance');
        $this->descricaoValida2 = new Descricao('Ficção Científica');
        $this->descricaoValida3 = new Descricao('Terror');
    });

    test('deve criar um Assunto e gerar um ID se não for fornecido', function () {
        $assunto = new Assunto(
            descricao: $this->descricaoValida1
        );

        expect($assunto)->toBeInstanceOf(Assunto::class)    
            ->and($assunto->id())->not()->toBeNull() 
            ->and($assunto->getDescricao()->value())->toBe('Romance');
    });

    test('deve criar um Assunto com descrição diferente e gerar ID único', function () {
        $assunto1 = new Assunto(
            descricao: $this->descricaoValida1
        );

        $assunto2 = new Assunto(
            descricao: $this->descricaoValida2
        );

        expect($assunto1->id())->not()->toBe($assunto2->id())
            ->and($assunto1->getDescricao()->value())->toBe('Romance')
            ->and($assunto2->getDescricao()->value())->toBe('Ficção Científica');
    });

    test('deve criar múltiplos Assuntos com diferentes descrições', function () {
        $assunto1 = new Assunto(descricao: $this->descricaoValida1);
        $assunto2 = new Assunto(descricao: $this->descricaoValida2);
        $assunto3 = new Assunto(descricao: $this->descricaoValida3);

        expect($assunto1->getDescricao()->value())->toBe('Romance')
            ->and($assunto2->getDescricao()->value())->toBe('Ficção Científica')
            ->and($assunto3->getDescricao()->value())->toBe('Terror')
            ->and($assunto1->id())->not()->toBe($assunto2->id())
            ->and($assunto2->id())->not()->toBe($assunto3->id())
            ->and($assunto1->id())->not()->toBe($assunto3->id());
    });

    test('deve validar que a descrição não pode estar vazia', function () {
        $descricaoVazia = new Descricao('');
        
        new Assunto(descricao: $descricaoVazia);
    })->throws(\InvalidArgumentException::class);

    test('deve validar que a descrição não pode exceder 40 caracteres', function () {
        $descricaoLonga = new Descricao(str_repeat('A', 41));
        
        new Assunto(descricao: $descricaoLonga);
    })->throws(\InvalidArgumentException::class);

    test('deve aceitar descrição com exatamente 40 caracteres', function () {
        $descricaoLimite = new Descricao(str_repeat('A', 40));
        $assunto = new Assunto(descricao: $descricaoLimite);

        expect($assunto->getDescricao()->value())->toBe(str_repeat('A', 40));
    });

    test('deve aceitar descrição com 1 caractere', function () {
        $descricaoMinima = new Descricao('A');
        $assunto = new Assunto(descricao: $descricaoMinima);

        expect($assunto->getDescricao()->value())->toBe('A');
    });

    test('deve aceitar descrição com 39 caracteres', function () {
        $descricaoValida = new Descricao(str_repeat('B', 39)); 
        $assunto = new Assunto(descricao: $descricaoValida);

        expect($assunto->getDescricao()->value())->toBe(str_repeat('B', 39));
    });

})->group('Entity', 'Domain', 'Assunto');

