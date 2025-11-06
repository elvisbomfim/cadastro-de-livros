<?php

namespace Tests\Unit\Models;

use App\Models\Assunto;

uses()->beforeEach(function () {
    $this->model = new Assunto();
});

describe('Model Assunto', function () {
    
    test('deve usar a tabela correta', function () {
        assertModelTable($this->model, 'assunto');
    });

    test('deve ter os campos fillable corretos', function () {
        assertModelFillable($this->model, ['id', 'descricao']);
    });

    test('deve usar UUID como chave primária', function () {
        assertModelUsesUuid($this->model);
    });

    test('deve ter método livros() definido', function () {
        assertModelHasMethod($this->model, 'livros');
    });

    test('deve ter timestamps habilitados', function () {
        assertModelHasTimestamps($this->model, true);
    });

})->group('Models', 'Unit', 'Assunto');

