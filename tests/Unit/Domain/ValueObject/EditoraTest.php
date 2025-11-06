<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Editora;
use InvalidArgumentException;

describe('Value Object Editora', function () {
    
    test('deve criar uma Editora válida', function () {
        $editora = new Editora('Editora Sextante');
        
        expect($editora->value())->toBe('Editora Sextante');
    });

    test('deve criar múltiplas Editoras diferentes com valores válidos', function () {
        $editora1 = new Editora('Editora Zênite');
        $editora2 = new Editora('Saraiva');
        $editora3 = new Editora('Companhia das Letras');
        
        expect($editora1->value())->toBe('Editora Zênite')
            ->and($editora2->value())->toBe('Saraiva')
            ->and($editora3->value())->toBe('Companhia das Letras');
    });

    test('deve aceitar editora com exatamente 40 caracteres', function () {
        $editora = new Editora(str_repeat('A', 40));
        
        expect(strlen($editora->value()))->toBe(40);
    });

    test('deve aceitar editora com 1 caractere', function () {
        $editora = new Editora('A');
        
        expect($editora->value())->toBe('A');
    });

    test('deve aceitar editora com 39 caracteres', function () {
        $editora = new Editora(str_repeat('B', 39));
        
        expect(strlen($editora->value()))->toBe(39);
    });

    test('deve lançar exceção ao criar editora com mais de 40 caracteres', function () {
        new Editora(str_repeat('C', 41));
    })->throws(InvalidArgumentException::class, 'Editora não pode ter mais de 40 caracteres.');

    test('deve lançar exceção ao criar editora com 50 caracteres', function () {
        new Editora(str_repeat('D', 50));
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar editora com 100 caracteres', function () {
        new Editora(str_repeat('E', 100));
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'Editora');

