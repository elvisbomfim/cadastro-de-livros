<?php

namespace Core\UseCase\Relatorio;

use Core\Domain\Repository\RelatorioRepositoryInterface;

class LivrosPorCategoriaUseCase
{
    public function __construct(
        private RelatorioRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->livrosPorCategoria();
    }
}

