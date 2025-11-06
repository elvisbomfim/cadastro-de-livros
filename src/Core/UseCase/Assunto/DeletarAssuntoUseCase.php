<?php

namespace Core\UseCase\Assunto;

use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;

class DeletarAssuntoUseCase
{
    public function __construct(
        private AssuntoRepositoryInterface $repository
    ) {}

    public function execute(string $id): void
    {
        $assunto = $this->repository->findById($id);
        
        if (!$assunto) {
            throw new LivroNaoEncontradoException("Assunto não encontrado com ID: {$id}");
        }

        $this->repository->delete($id);
    }
}

