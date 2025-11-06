<?php

namespace Core\UseCase\Livro;

use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;

class DeletarLivroUseCase
{
    public function __construct(
        private LivroRepositoryInterface $repository
    ) {}

    public function execute(string $id): void
    {
        $livro = $this->repository->findById($id);
        
        if (!$livro) {
            throw new LivroNaoEncontradoException("Livro não encontrado com ID: {$id}");
        }

        $this->repository->delete($id);
    }
}

