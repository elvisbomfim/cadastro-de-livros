<?php

namespace Core\UseCase\Livro;

use Core\Domain\Repository\LivroRepositoryInterface;

class ListarLivrosUseCase
{
    public function __construct(
        private LivroRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}

