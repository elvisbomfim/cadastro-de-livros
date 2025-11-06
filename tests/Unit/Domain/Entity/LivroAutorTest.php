<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Livro;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\Exception\EntityValidationException;

describe('Relacionamento Livro-Autor', function () {
    
    beforeEach(function () {
        $this->tituloValido = new Titulo('O Segredo da Mente Milionária'); 
        $this->editoraValida = new Editora('Editora Sextante');
        $this->edicaoInicial = new Edicao(1);
        $this->anoValido = new AnoPublicacao(2005);
        $this->preco = new Moeda(29.90);
    });

    test('deve adicionar um autor ao Livro', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->adicionarAutor(10);
        
        expect($livro->autores())->toBe([10])
            ->and(count($livro->autores()))->toBe(1);
    });

    test('deve adicionar múltiplos autores diferentes ao Livro', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);
        $livro->adicionarAutor(30);
        
        expect($livro->autores())->toBe([10, 20, 30])
            ->and(count($livro->autores()))->toBe(3);
    });

    test('deve adicionar autores em ordens diferentes e manter a ordem', function () {
        $livro1 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro2 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro1->adicionarAutor(10);
        $livro1->adicionarAutor(20);
        
        $livro2->adicionarAutor(20);
        $livro2->adicionarAutor(10);
        
        expect($livro1->autores())->toBe([10, 20])
            ->and($livro2->autores())->toBe([20, 10])
            ->and($livro1->autores())->not()->toBe($livro2->autores());
    });

    test('não deve permitir adicionar o mesmo autor duas vezes', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(50);
        
        $livro->adicionarAutor(50);
    })->throws(EntityValidationException::class, 'Autor já adicionado');

    test('deve remover um autor específico mantendo os outros', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);
        $livro->adicionarAutor(30);

        $livro->removerAutor(20);
        
        expect($livro->autores())->toBe([10, 30])
            ->and(count($livro->autores()))->toBe(2);
    });

    test('deve remover múltiplos autores diferentes', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);
        $livro->adicionarAutor(30);
        $livro->adicionarAutor(40);

        $livro->removerAutor(20);
        $livro->removerAutor(40);
        
        expect($livro->autores())->toBe([10, 30])
            ->and(count($livro->autores()))->toBe(2);
    });

    test('deve lançar exceção ao tentar remover autor inexistente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->removerAutor(999);
    })->throws(EntityValidationException::class, 'Autor não encontrado');

    test('deve lançar exceção ao tentar remover diferentes autores inexistentes', function () {
        $livro1 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro2 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        try {
            $livro1->removerAutor(100);
        } catch (EntityValidationException $e) {
            expect($e->getMessage())->toBe('Autor não encontrado');
        }
        
        try {
            $livro2->removerAutor(200);
        } catch (EntityValidationException $e) {
            expect($e->getMessage())->toBe('Autor não encontrado');
        }
    });

    test('deve manter autores após remover e adicionar novamente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);
        $livro->removerAutor(10);
        $livro->adicionarAutor(30);
        
        expect($livro->autores())->toBe([20, 30])
            ->and(count($livro->autores()))->toBe(2);
    });

    test('deve retornar array vazio quando não há autores', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        expect($livro->autores())->toBe([])
            ->and(count($livro->autores()))->toBe(0);
    });

})->group('Entity', 'Domain', 'Livro', 'Relacionamento');

