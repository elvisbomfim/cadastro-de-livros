<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Assunto;

interface AssuntoRepositoryInterface
{
    public function create(Assunto $assunto): Assunto;
    public function findById(string $id): ?Assunto;
    public function findAll(): array;
    public function update(Assunto $assunto): Assunto;
    public function delete(string $id): void;
}

