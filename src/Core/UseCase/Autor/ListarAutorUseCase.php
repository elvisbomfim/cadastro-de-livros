<?php

namespace Core\UseCase\Autor;

use Core\Domain\Entity\Autor;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;

class ListarAutorUseCase
{
    public function __construct(
        private AutorRepositoryInterface $repository
    ) {}

    public function execute(string $id): Autor
    {
        $autor = $this->repository->findById($id);
        
        if (!$autor) {
            throw new LivroNaoEncontradoException("Autor não encontrado com ID: {$id}");
        }

        return $autor;
    }
}

