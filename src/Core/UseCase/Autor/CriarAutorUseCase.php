<?php

namespace Core\UseCase\Autor;

use Core\Domain\Entity\Autor;
use Core\Domain\Repository\AutorRepositoryInterface;
use Core\Domain\ValueObject\Nome;

class CriarAutorUseCase
{
    public function __construct(
        private AutorRepositoryInterface $repository
    ) {}

    public function execute(string $nome): Autor
    {
        $autor = new Autor(nome: new Nome($nome));
        return $this->repository->create($autor);
    }
}
