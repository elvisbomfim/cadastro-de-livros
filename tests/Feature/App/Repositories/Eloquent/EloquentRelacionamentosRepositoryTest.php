<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Entity\Autor;
use Core\Domain\Entity\Assunto;
use Core\Domain\Entity\Livro;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Descricao;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Eloquent Repository - Integração Completa', function () {
    
    test('deve criar livro com múltiplos autores e assuntos usando repositórios', function () {
        $livroRepository = app(LivroRepositoryInterface::class);
        $autorRepository = app(AutorRepositoryInterface::class);
        $assuntoRepository = app(AssuntoRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        $autor2 = new Autor(nome: new Nome('Clarice Lispector'));

        $assunto1 = new Assunto(descricao: new Descricao('Romance'));
        $assunto2 = new Assunto(descricao: new Descricao('Ficção'));

        $livroCriado = $livroRepository->create($livro);
        $autor1Criado = $autorRepository->create($autor1);
        $autor2Criado = $autorRepository->create($autor2);
        $assunto1Criado = $assuntoRepository->create($assunto1);
        $assunto2Criado = $assuntoRepository->create($assunto2);

        $livroCriado->adicionarAutor($autor1Criado->getId()->value());
        $livroCriado->adicionarAutor($autor2Criado->getId()->value());
        $livroCriado->adicionarAssunto($assunto1Criado->getId()->value());
        $livroCriado->adicionarAssunto($assunto2Criado->getId()->value());

        $livroAtualizado = $livroRepository->update($livroCriado);

        expect($livroAtualizado->autores())->toHaveCount(2)
            ->and($livroAtualizado->assuntos())->toHaveCount(2);
    });

    test('deve encontrar livro com relacionamentos através do repositório', function () {
        $livroRepository = app(LivroRepositoryInterface::class);
        $autorRepository = app(AutorRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $autor = new Autor(nome: new Nome('Machado de Assis'));

        $livroCriado = $livroRepository->create($livro);
        $autorCriado = $autorRepository->create($autor);

        $livroCriado->adicionarAutor($autorCriado->getId()->value());
        $livroRepository->update($livroCriado);

        $livroEncontrado = $livroRepository->findById($livroCriado->getId()->value());

        expect($livroEncontrado)->not()->toBeNull()
            ->and($livroEncontrado->getId()->value())->toBe($livroCriado->getId()->value())
            ->and($livroEncontrado->autores())->toHaveCount(1);
    });

    test('deve atualizar relacionamentos de livro através do repositório', function () {
        $livroRepository = app(LivroRepositoryInterface::class);
        $autorRepository = app(AutorRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $autor1 = new Autor(nome: new Nome('Machado de Assis'));
        $autor2 = new Autor(nome: new Nome('Clarice Lispector'));

        $livroCriado = $livroRepository->create($livro);
        $autor1Criado = $autorRepository->create($autor1);
        $autor2Criado = $autorRepository->create($autor2);

        $livroCriado->adicionarAutor($autor1Criado->getId()->value());
        $livroRepository->update($livroCriado);

        $livroEncontrado = $livroRepository->findById($livroCriado->getId()->value());
        $livroEncontrado->removerAutor($autor1Criado->getId()->value());
        $livroEncontrado->adicionarAutor($autor2Criado->getId()->value());

        $livroAtualizado = $livroRepository->update($livroEncontrado);

        expect($livroAtualizado->autores())->toHaveCount(1)
            ->and($livroAtualizado->autores()[0])->toBe($autor2Criado->getId()->value());
    });

    test('deve deletar livro e remover relacionamentos através do repositório', function () {
        $livroRepository = app(LivroRepositoryInterface::class);
        $autorRepository = app(AutorRepositoryInterface::class);
        $assuntoRepository = app(AssuntoRepositoryInterface::class);
        
        $livro = new Livro(
            titulo: new Titulo('Dom Casmurro'),
            editora: new Editora('Editora Globo'),
            edicao: new Edicao(1),
            anoPublicacao: new AnoPublicacao(1899),
            preco: new Moeda(35.50)
        );

        $autor = new Autor(nome: new Nome('Machado de Assis'));
        $assunto = new Assunto(descricao: new Descricao('Romance'));

        $livroCriado = $livroRepository->create($livro);
        $autorCriado = $autorRepository->create($autor);
        $assuntoCriado = $assuntoRepository->create($assunto);

        $livroCriado->adicionarAutor($autorCriado->getId()->value());
        $livroCriado->adicionarAssunto($assuntoCriado->getId()->value());
        $livroRepository->update($livroCriado);

        $livroId = $livroCriado->getId()->value();
        $livroRepository->delete($livroId);
        
        $livroEncontrado = $livroRepository->findById($livroId);

        expect($livroEncontrado)->toBeNull()
            ->and(\DB::table('livro_autor')->where('livro_id', $livroId)->exists())->toBeFalse()
            ->and(\DB::table('livro_assunto')->where('livro_id', $livroId)->exists())->toBeFalse();
    });

})->group('Eloquent', 'Feature', 'Repository', 'Relacionamentos');
