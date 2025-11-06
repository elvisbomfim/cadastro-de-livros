<?php

namespace Core\UseCase\Assunto;

use Core\Domain\Entity\Assunto;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\ValueObject\Uuid;

class AtualizarAssuntoUseCase
{
    public function __construct(
        private AssuntoRepositoryInterface $repository
    ) {}

    public function execute(string $id, string $descricao): Assunto
    {
        $assunto = $this->repository->findById($id);
        
        if (!$assunto) {
            throw new LivroNaoEncontradoException("Assunto não encontrado com ID: {$id}");
        }

        $assuntoAtualizado = new Assunto(
            descricao: new Descricao($descricao),
            id: new Uuid($id)
        );

        return $this->repository->update($assuntoAtualizado);
    }
}

