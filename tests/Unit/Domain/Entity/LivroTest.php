<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Livro;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\Exception\EntityValidationException;

describe('Entidade Livro', function () {
    
    beforeEach(function () {
        $this->tituloValido = new Titulo('O Segredo da Mente Milionária'); 
        $this->editoraValida = new Editora('Editora Sextante');
        $this->edicaoInicial = new Edicao(1);
        $this->anoValido = new AnoPublicacao(2005);
        $this->preco = new Moeda(29.90);
    });

    test('deve criar um Livro e gerar um ID se não for fornecido', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        expect($livro)->toBeInstanceOf(Livro::class)    
            ->and($livro->id())->not()->toBeNull() 
            ->and($livro->getTitulo()->value())->toBe('O Segredo da Mente Milionária')
            ->and($livro->getEdicao()->value())->toBe(1)
            ->and($livro->getPreco()->value())->toBe(29.90);
    });
    
    test('deve permitir a atualização da edição para um valor maior', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->atualizarEdicao(2);
        
        expect($livro->getEdicao()->value())->toBe(2);       
    });

    test('deve lançar exceção ao tentar atualizar a edição para um valor inválido (menor ou igual ao atual)', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(3),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->atualizarEdicao(2);

        $livro2 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(3),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro2->atualizarEdicao(3);       
        
        
    })->throws(EntityValidationException::class, 'A nova edição deve ser maior que a edição atual');


    test('deve adicionar um autor ao Livro e manter os IDs de autores em um array', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,  
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);
        
        expect($livro->autores())->toBe([10, 20])
            ->and(count($livro->autores()))->toBe(2);
    });

    test('não deve duplicar o ID de um autor se for adicionado novamente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(50);
        
        $livro->adicionarAutor(50);
    })->throws(EntityValidationException::class, 'Autor já adicionado');
    
    test('deve remover um autor do Livro', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor(10);
        $livro->adicionarAutor(20);

        $livro->removerAutor(10);
        
        expect($livro->autores())->toBe([20]);
    });

    test('deve criar Livro com diferentes preços em R$', function () {
        $livro1 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: new Moeda(29.90)
        );

        $livro2 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: new Moeda(49.99)
        );

        $livro3 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: new Moeda(99.50)
        );

        expect($livro1->getPreco()->value())->toBe(29.90)
            ->and($livro2->getPreco()->value())->toBe(49.99)
            ->and($livro3->getPreco()->value())->toBe(99.50);
    });

    test('deve formatar preço do Livro corretamente como R$', function () {
        $livro1 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: new Moeda(29.90)
        );

        $livro2 = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: new Edicao(1),
            anoPublicacao: $this->anoValido,
            preco: new Moeda(1000.50)
        );

        expect($livro1->getPreco()->formatar())->toBe('R$ 29,90')
            ->and($livro2->getPreco()->formatar())->toBe('R$ 1.000,50');
    });

})->group('Entity', 'Domain', 'Livro');
