<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Livro;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\Exception\EntityValidationException;

describe('Relacionamento Livro-Assunto', function () {
    
    beforeEach(function () {
        $this->tituloValido = new Titulo('O Segredo da Mente Milionária'); 
        $this->editoraValida = new Editora('Editora Sextante');
        $this->edicaoInicial = new Edicao(1);
        $this->anoValido = new AnoPublicacao(2005);
        $this->preco = new Moeda(29.90);
    });

    test('deve adicionar um assunto ao Livro', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        
        expect($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000001'])
            ->and(count($livro->assuntos()))->toBe(1);
    });

    test('deve adicionar múltiplos assuntos diferentes ao Livro', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000003');
        
        expect($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002', '00000000-0000-0000-0000-000000000003'])
            ->and(count($livro->assuntos()))->toBe(3);
    });

    test('deve adicionar assuntos em ordens diferentes e manter a ordem', function () {
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
        
        $livro1->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro1->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        
        $livro2->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        $livro2->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        
        expect($livro1->assuntos())->toBe(['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002'])
            ->and($livro2->assuntos())->toBe(['00000000-0000-0000-0000-000000000002', '00000000-0000-0000-0000-000000000001'])
            ->and($livro1->assuntos())->not()->toBe($livro2->assuntos());
    });

    test('não deve permitir adicionar o mesmo assunto duas vezes', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000005');
        
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000005');
    })->throws(EntityValidationException::class, 'Assunto já adicionado');

    test('deve remover um assunto específico mantendo os outros', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000003');

        $livro->removerAssunto('00000000-0000-0000-0000-000000000002');
        
        expect($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000003'])
            ->and(count($livro->assuntos()))->toBe(2);
    });

    test('deve remover múltiplos assuntos diferentes', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000003');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000004');

        $livro->removerAssunto('00000000-0000-0000-0000-000000000002');
        $livro->removerAssunto('00000000-0000-0000-0000-000000000004');
        
        expect($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000003'])
            ->and(count($livro->assuntos()))->toBe(2);
    });

    test('deve lançar exceção ao tentar remover assunto inexistente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->removerAssunto('00000000-0000-0000-0000-000000000999');
    })->throws(EntityValidationException::class, 'Assunto não encontrado');

    test('deve lançar exceção ao tentar remover diferentes assuntos inexistentes', function () {
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
            $livro1->removerAssunto('00000000-0000-0000-0000-000000000100');
        } catch (EntityValidationException $e) {
            expect($e->getMessage())->toBe('Assunto não encontrado');
        }
        
        try {
            $livro2->removerAssunto('00000000-0000-0000-0000-000000000200');
        } catch (EntityValidationException $e) {
            expect($e->getMessage())->toBe('Assunto não encontrado');
        }
    });

    test('deve manter assuntos após remover e adicionar novamente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        $livro->removerAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000003');
        
        expect($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000002', '00000000-0000-0000-0000-000000000003'])
            ->and(count($livro->assuntos()))->toBe(2);
    });

    test('deve retornar array vazio quando não há assuntos', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );
        
        expect($livro->assuntos())->toBe([])
            ->and(count($livro->assuntos()))->toBe(0);
    });

    test('deve gerenciar autores e assuntos independentemente', function () {
        $livro = new Livro(
            titulo: $this->tituloValido,
            editora: $this->editoraValida,
            edicao: $this->edicaoInicial,
            anoPublicacao: $this->anoValido,
            preco: $this->preco
        );

        $livro->adicionarAutor('00000000-0000-0000-0000-000000000010');
        $livro->adicionarAutor('00000000-0000-0000-0000-000000000020');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000001');
        $livro->adicionarAssunto('00000000-0000-0000-0000-000000000002');
        
        expect($livro->autores())->toBe(['00000000-0000-0000-0000-000000000010', '00000000-0000-0000-0000-000000000020'])
            ->and($livro->assuntos())->toBe(['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002'])
            ->and(count($livro->autores()))->toBe(2)
            ->and(count($livro->assuntos()))->toBe(2);
    });

})->group('Entity', 'Domain', 'Livro', 'Relacionamento');
