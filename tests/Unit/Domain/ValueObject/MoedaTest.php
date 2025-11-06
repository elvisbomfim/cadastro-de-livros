<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Moeda;
use InvalidArgumentException;

describe('Value Object Moeda', function () {
    
    test('deve criar uma Moeda válida em R$', function () {
        $moeda = new Moeda(29.90);
        
        expect($moeda->value())->toBe(29.90);
    });

    test('deve criar múltiplas Moedas diferentes com valores válidos em R$', function () {
        $moeda1 = new Moeda(29.90);
        $moeda2 = new Moeda(49.99);
        $moeda3 = new Moeda(99.50);
        
        expect($moeda1->value())->toBe(29.90)
            ->and($moeda2->value())->toBe(49.99)
            ->and($moeda3->value())->toBe(99.50);
    });

    test('deve aceitar moeda com valor zero', function () {
        $moeda = new Moeda(0.00);
        
        expect($moeda->value())->toBe(0.00);
    });

    test('deve aceitar moeda com valores decimais diferentes', function () {
        $moeda1 = new Moeda(10.50);
        $moeda2 = new Moeda(25.75);
        $moeda3 = new Moeda(100.99);
        
        expect($moeda1->value())->toBe(10.50)
            ->and($moeda2->value())->toBe(25.75)
            ->and($moeda3->value())->toBe(100.99);
    });

    test('deve aceitar moeda com valores inteiros', function () {
        $moeda1 = new Moeda(10.0);
        $moeda2 = new Moeda(50.0);
        $moeda3 = new Moeda(100.0);
        
        expect($moeda1->value())->toBe(10.0)
            ->and($moeda2->value())->toBe(50.0)
            ->and($moeda3->value())->toBe(100.0);
    });

    test('deve formatar moeda corretamente como R$', function () {
        $moeda1 = new Moeda(29.90);
        $moeda2 = new Moeda(1000.50);
        $moeda3 = new Moeda(0.99);
        
        expect($moeda1->formatar())->toBe('R$ 29,90')
            ->and($moeda2->formatar())->toBe('R$ 1.000,50')
            ->and($moeda3->formatar())->toBe('R$ 0,99');
    });

    test('deve lançar exceção ao criar moeda com valor negativo', function () {
        new Moeda(-10.50);
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar moeda com diferentes valores negativos', function () {
        try {
            new Moeda(-1.00);
        } catch (InvalidArgumentException $e) {
            expect($e->getMessage())->toContain('Moeda não pode ser menor que');
        }
        
        try {
            new Moeda(-100.50);
        } catch (InvalidArgumentException $e) {
            expect($e->getMessage())->toContain('Moeda não pode ser menor que');
        }
        
        try {
            new Moeda(-0.01);
        } catch (InvalidArgumentException $e) {
            expect($e->getMessage())->toContain('Moeda não pode ser menor que');
        }
    });

    test('deve aceitar valores grandes válidos', function () {
        $moeda1 = new Moeda(1000.00);
        $moeda2 = new Moeda(9999.99);
        $moeda3 = new Moeda(50000.50);
        
        expect($moeda1->value())->toBe(1000.00)
            ->and($moeda2->value())->toBe(9999.99)
            ->and($moeda3->value())->toBe(50000.50);
    });

})->group('ValueObject', 'Domain', 'Moeda');

