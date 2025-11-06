<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Livro;

interface LivroRepositoryInterface
{
    public function create(Livro $livro): Livro;
    public function findById(string $id): ?Livro;
    public function findAll(): array;
    public function update(Livro $livro): Livro;
    public function delete(string $id): void;
}

