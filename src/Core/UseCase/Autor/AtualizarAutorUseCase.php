<?php

namespace Core\UseCase\Autor;

use Core\Domain\Entity\Autor;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Nome;
use Core\Domain\ValueObject\Uuid;

class AtualizarAutorUseCase
{
    public function __construct(
        private AutorRepositoryInterface $repository
    ) {}

    public function execute(string $id, string $nome): Autor
    {
        $autor = $this->repository->findById($id);
        
        if (!$autor) {
            throw new LivroNaoEncontradoException("Autor não encontrado com ID: {$id}");
        }

        $autorAtualizado = new Autor(
            nome: new Nome($nome),
            id: new Uuid($id)
        );

        return $this->repository->update($autorAtualizado);
    }
}

