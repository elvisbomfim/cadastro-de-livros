<?php

namespace Core\UseCase\Livro;

use Core\Domain\Entity\Livro;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;
use Core\Domain\ValueObject\Uuid;

class AtualizarLivroUseCase
{
    public function __construct(
        private LivroRepositoryInterface $repository
    ) {}

    public function execute(
        string $id,
        string $titulo,
        string $editora,
        int $edicao,
        int $anoPublicacao,
        float $preco
    ): Livro {
        $livro = $this->repository->findById($id);
        
        if (!$livro) {
            throw new LivroNaoEncontradoException("Livro não encontrado com ID: {$id}");
        }

        $livroAtualizado = new Livro(
            titulo: new Titulo($titulo),
            editora: new Editora($editora),
            edicao: new Edicao($edicao),
            anoPublicacao: new AnoPublicacao($anoPublicacao),
            preco: new Moeda($preco),
            id: new Uuid($id)
        );

        return $this->repository->update($livroAtualizado);
    }
}

