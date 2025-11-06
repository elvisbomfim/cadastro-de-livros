<?php

namespace Tests\Unit\Models;

use App\Models\LivroAutor;

uses()->beforeEach(function () {
    $this->model = new LivroAutor();
});

describe('Model LivroAutor', function () {
    
    test('deve usar a tabela correta', function () {
        assertModelTable($this->model, 'livro_autor');
    });

    test('deve ter os campos fillable corretos', function () {
        assertModelFillable($this->model, ['livro_id', 'autor_id']);
    });

    test('deve ter incrementing desabilitado', function () {
        expect($this->model->getIncrementing())->toBeFalse();
    });

    test('deve ter timestamps desabilitados', function () {
        assertModelHasTimestamps($this->model, false);
    });

    test('deve ter método livro() definido', function () {
        assertModelHasMethod($this->model, 'livro');
    });

    test('deve ter método autor() definido', function () {
        assertModelHasMethod($this->model, 'autor');
    });

})->group('Models', 'Unit', 'LivroAutor');

