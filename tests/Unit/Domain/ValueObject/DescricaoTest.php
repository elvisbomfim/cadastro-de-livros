<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Descricao;
use InvalidArgumentException;

describe('Value Object Descricao', function () {
    
    test('deve criar uma Descrição válida', function () {
        $descricao = new Descricao('Romance contemporâneo');
        
        expect($descricao->value())->toBe('Romance contemporâneo');
    });

    test('deve criar múltiplas Descrições diferentes com valores válidos', function () {
        $descricao1 = new Descricao('Ficção científica');
        $descricao2 = new Descricao('Terror psicológico');
        $descricao3 = new Descricao('Aventura');
        
        expect($descricao1->value())->toBe('Ficção científica')
            ->and($descricao2->value())->toBe('Terror psicológico')
            ->and($descricao3->value())->toBe('Aventura');
    });

    test('deve aceitar descrição com exatamente 40 caracteres', function () {
        $descricao = new Descricao(str_repeat('A', 40));
        
        expect(strlen($descricao->value()))->toBe(40);
    });

    test('deve aceitar descrição com 1 caractere', function () {
        $descricao = new Descricao('A');
        
        expect($descricao->value())->toBe('A');
    });

    test('deve aceitar descrição com 39 caracteres', function () {
        $descricao = new Descricao(str_repeat('B', 39));
        
        expect(strlen($descricao->value()))->toBe(39);
    });

    test('deve lançar exceção ao criar descrição com mais de 40 caracteres', function () {
        new Descricao(str_repeat('C', 41));
    })->throws(InvalidArgumentException::class, 'Descrição não pode ter mais de 40 caracteres.');

    test('deve lançar exceção ao criar descrição com 50 caracteres', function () {
        new Descricao(str_repeat('D', 50));
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar descrição com 100 caracteres', function () {
        new Descricao(str_repeat('E', 100));
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'Descricao');

