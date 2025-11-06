<?php

namespace Tests\Unit\Models;

use App\Models\LivroAssunto;

uses()->beforeEach(function () {
    $this->model = new LivroAssunto();
});

describe('Model LivroAssunto', function () {
    
    test('deve usar a tabela correta', function () {
        assertModelTable($this->model, 'livro_assunto');
    });

    test('deve ter os campos fillable corretos', function () {
        assertModelFillable($this->model, ['livro_id', 'assunto_id']);
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

    test('deve ter método assunto() definido', function () {
        assertModelHasMethod($this->model, 'assunto');
    });

})->group('Models', 'Unit', 'LivroAssunto');

