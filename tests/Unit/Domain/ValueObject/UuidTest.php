<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\ValueObject\Uuid;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

describe('Value Object Uuid', function () {
    
    test('deve criar um UUID válido', function () {
        $uuid = Uuid::random();
        
        expect($uuid)->toBeInstanceOf(Uuid::class)
            ->and($uuid->__toString())->not()->toBeEmpty()
            ->and(RamseyUuid::isValid($uuid->__toString()))->toBeTrue();
    });

    test('deve criar múltiplos UUIDs diferentes', function () {
        $uuid1 = Uuid::random();
        $uuid2 = Uuid::random();
        $uuid3 = Uuid::random();
        
        expect($uuid1->__toString())->not()->toBe($uuid2->__toString())
            ->and($uuid2->__toString())->not()->toBe($uuid3->__toString())
            ->and($uuid1->__toString())->not()->toBe($uuid3->__toString())
            ->and(RamseyUuid::isValid($uuid1->__toString()))->toBeTrue()
            ->and(RamseyUuid::isValid($uuid2->__toString()))->toBeTrue()
            ->and(RamseyUuid::isValid($uuid3->__toString()))->toBeTrue();
    });

    test('deve criar UUID a partir de string válida', function () {
        $uuidString = RamseyUuid::uuid4()->toString();
        $uuid = new Uuid($uuidString);
        
        expect($uuid->__toString())->toBe($uuidString);
    });

    test('deve criar múltiplos UUIDs a partir de strings válidas diferentes', function () {
        $uuidString1 = RamseyUuid::uuid4()->toString();
        $uuidString2 = RamseyUuid::uuid4()->toString();
        $uuidString3 = RamseyUuid::uuid4()->toString();
        
        $uuid1 = new Uuid($uuidString1);
        $uuid2 = new Uuid($uuidString2);
        $uuid3 = new Uuid($uuidString3);
        
        expect($uuid1->__toString())->toBe($uuidString1)
            ->and($uuid2->__toString())->toBe($uuidString2)
            ->and($uuid3->__toString())->toBe($uuidString3);
    });

    test('deve lançar exceção ao criar UUID com string inválida', function () {
        new Uuid('invalid-uuid-string');
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar UUID com string vazia', function () {
        new Uuid('');
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar UUID com string que não é UUID', function () {
        new Uuid('12345');
    })->throws(InvalidArgumentException::class);

    test('deve lançar exceção ao criar UUID com formato incorreto', function () {
        new Uuid('not-a-valid-uuid-format');
    })->throws(InvalidArgumentException::class);

})->group('ValueObject', 'Domain', 'Uuid');

