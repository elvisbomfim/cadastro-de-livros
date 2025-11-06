<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Titulo;
use InvalidArgumentException;

describe('Value Object Titulo', function () {
    
    test('deve criar um Título válido', function () {
        $titulo = new Titulo('A Revolta de Atlas');
        
        expect($titulo->value())->toBe('A Revolta de Atlas');
    });

    test('deve criar múltiplos Títulos diferentes com valores válidos', function () {
        $titulo1 = new Titulo('O Segredo da Mente Milionária');
        $titulo2 = new Titulo('Pai Rico, Pai Pobre');
        $titulo3 = new Titulo('Dom Casmurro');
        
        expect($titulo1->value())->toBe('O Segredo da Mente Milionária')
            ->and($titulo2->value())->toBe('Pai Rico, Pai Pobre')
            ->and($titulo3->value())->toBe('Dom Casmurro');
    });

    test('deve aceitar título com exatamente 40 caracteres', function () {
        $titulo = new Titulo(str_repeat('A', 40));
        
        expect(strlen($titulo->value()))->toBe(40);
    });

    test('deve aceitar título com 1 caractere', function () {
        $titulo = new Titulo('A');
        
        expect($titulo->value())->toBe('A');
    });

    test('deve aceitar título com 39 caracteres', function () {
        $titulo = new Titulo(str_repeat('B', 39));
        
        expect(strlen($titulo->value()))->toBe(39);
    });

    test('deve lançar exceção ao criar título vazio', function () {
        new Titulo('');
    })->throws(InvalidArgumentException::class, 'Título não pode ser vazio.');

    test('deve lançar exceção ao criar título com mais de 40 caracteres', function () {
        new Titulo(str_repeat('C', 41));
    })->throws(InvalidArgumentException::class, 'Título não pode ter mais de 40 caracteres.');

    test('deve lançar exceção ao criar título com 50 caracteres', function () {
        new Titulo(str_repeat('D', 50));
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar título com 100 caracteres', function () {
        new Titulo(str_repeat('E', 100));
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'Titulo');

