<?php

namespace Core\UseCase\Assunto;

use Core\Domain\Repository\AssuntoRepositoryInterface;

class ListarAssuntosUseCase
{
    public function __construct(
        private AssuntoRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}

