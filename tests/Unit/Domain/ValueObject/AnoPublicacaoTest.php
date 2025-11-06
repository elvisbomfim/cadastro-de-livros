<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\AnoPublicacao;
use InvalidArgumentException;

describe('Value Object AnoPublicacao', function () {
    
    test('deve criar um Ano de Publicação válido', function () {
        $ano = new AnoPublicacao(2005);
        
        expect($ano->value())->toBe(2005);
    });

    test('deve criar múltiplos Anos diferentes com valores válidos', function () {
        $ano1 = new AnoPublicacao(1957);
        $ano2 = new AnoPublicacao(2000);
        $ano3 = new AnoPublicacao(2023);
        
        expect($ano1->value())->toBe(1957)
            ->and($ano2->value())->toBe(2000)
            ->and($ano3->value())->toBe(2023);
    });

    test('deve aceitar ano com valor mínimo de 1000', function () {
        $ano = new AnoPublicacao(1000);
        
        expect($ano->value())->toBe(1000);
    });

    test('deve aceitar ano com valor maior que 1000', function () {
        $ano1 = new AnoPublicacao(1001);
        $ano2 = new AnoPublicacao(1500);
        $ano3 = new AnoPublicacao(2024);
        
        expect($ano1->value())->toBe(1001)
            ->and($ano2->value())->toBe(1500)
            ->and($ano3->value())->toBe(2024);
    });

    test('deve lançar exceção ao criar ano menor que 1000', function () {
        new AnoPublicacao(999);
    })->throws(InvalidArgumentException::class, 'Ano de publicação não pode ser menor que 1000.');

    test('deve lançar exceção ao criar ano com valor 500', function () {
        new AnoPublicacao(500);
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar ano com valor 0', function () {
        new AnoPublicacao(0);
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar ano com valor negativo', function () {
        new AnoPublicacao(-100);
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'AnoPublicacao');

