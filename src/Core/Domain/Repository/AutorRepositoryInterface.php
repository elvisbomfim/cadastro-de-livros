<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Autor;

interface AutorRepositoryInterface
{
    public function create(Autor $autor): Autor;
    public function findById(string $id): ?Autor;
    public function findAll(): array;
    public function update(Autor $autor): Autor;
    public function delete(string $id): void;
}

