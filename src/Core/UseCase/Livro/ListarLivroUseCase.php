<?php

namespace Core\UseCase\Livro;

use Core\Domain\Entity\Livro;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Uuid;

class ListarLivroUseCase
{
    public function __construct(
        private LivroRepositoryInterface $repository
    ) {}

    public function execute(string $id): Livro
    {
        $livro = $this->repository->findById($id);
        
        if (!$livro) {
            throw new LivroNaoEncontradoException("Livro não encontrado com ID: {$id}");
        }

        return $livro;
    }
}

