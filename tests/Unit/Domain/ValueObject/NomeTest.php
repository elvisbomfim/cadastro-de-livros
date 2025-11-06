<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Nome;
use InvalidArgumentException;

describe('Value Object Nome', function () {
    
    test('deve criar um Nome válido', function () {
        $nome = new Nome('João Silva');
        
        expect($nome->value())->toBe('João Silva');
    });

    test('deve criar múltiplos Nomes diferentes com valores válidos', function () {
        $nome1 = new Nome('Maria Santos');
        $nome2 = new Nome('Pedro Oliveira');
        $nome3 = new Nome('Ana Costa');
        
        expect($nome1->value())->toBe('Maria Santos')
            ->and($nome2->value())->toBe('Pedro Oliveira')
            ->and($nome3->value())->toBe('Ana Costa');
    });

    test('deve aceitar nome com exatamente 40 caracteres', function () {
        $nome = new Nome(str_repeat('A', 40));
        
        expect(strlen($nome->value()))->toBe(40);
    });

    test('deve aceitar nome com 1 caractere', function () {
        $nome = new Nome('A');
        
        expect($nome->value())->toBe('A');
    });

    test('deve aceitar nome com 39 caracteres', function () {
        $nome = new Nome(str_repeat('B', 39));
        
        expect(strlen($nome->value()))->toBe(39);
    });

    test('deve lançar exceção ao criar nome com mais de 40 caracteres', function () {
        new Nome(str_repeat('C', 41));
    })->throws(InvalidArgumentException::class, 'Nome não pode ter mais de 40 caracteres.');

    test('deve lançar exceção ao criar nome com 50 caracteres', function () {
        new Nome(str_repeat('D', 50));
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar nome com 100 caracteres', function () {
        new Nome(str_repeat('E', 100));
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'Nome');

