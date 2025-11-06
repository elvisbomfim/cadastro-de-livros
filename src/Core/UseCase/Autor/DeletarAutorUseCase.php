<?php

namespace Core\UseCase\Autor;

use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;

class DeletarAutorUseCase
{
    public function __construct(
        private AutorRepositoryInterface $repository
    ) {}

    public function execute(string $id): void
    {
        $autor = $this->repository->findById($id);
        
        if (!$autor) {
            throw new LivroNaoEncontradoException("Autor não encontrado com ID: {$id}");
        }

        $this->repository->delete($id);
    }
}

