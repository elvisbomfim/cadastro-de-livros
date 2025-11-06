<?php

namespace Tests\Unit\Models;

use App\Models\Livro;

uses()->beforeEach(function () {
    $this->model = new Livro();
});

describe('Model Livro', function () {
    
    test('deve usar a tabela correta', function () {
        assertModelTable($this->model, 'livro');
    });

    test('deve ter os campos fillable corretos', function () {
        assertModelFillable($this->model, ['id', 'titulo', 'editora', 'edicao', 'ano_publicacao', 'preco']);
    });

    test('deve ter os casts corretos', function () {
        assertModelHasCasts($this->model, ['edicao', 'ano_publicacao', 'preco']);
    });

    test('deve usar UUID como chave primária', function () {
        assertModelUsesUuid($this->model);
    });

    test('deve ter método autors() definido', function () {
        assertModelHasMethod($this->model, 'autors');
    });

    test('deve ter método assuntos() definido', function () {
        assertModelHasMethod($this->model, 'assuntos');
    });

    test('deve ter timestamps habilitados', function () {
        assertModelHasTimestamps($this->model, true);
    });

})->group('Models', 'Unit', 'Livro');

