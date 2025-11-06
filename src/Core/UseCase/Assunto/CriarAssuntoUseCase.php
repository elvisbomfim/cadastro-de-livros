<?php

namespace Core\UseCase\Assunto;

use Core\Domain\Entity\Assunto;
use Core\Domain\Repository\AssuntoRepositoryInterface;
use Core\Domain\ValueObject\Descricao;

class CriarAssuntoUseCase
{
    public function __construct(
        private AssuntoRepositoryInterface $repository
    ) {}

    public function execute(string $descricao): Assunto
    {
        $assunto = new Assunto(descricao: new Descricao($descricao));
        return $this->repository->create($assunto);
    }
}
