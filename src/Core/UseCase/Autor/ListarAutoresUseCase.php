<?php

namespace Core\UseCase\Autor;

use Core\Domain\Repository\AutorRepositoryInterface;

class ListarAutoresUseCase
{
    public function __construct(
        private AutorRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}

