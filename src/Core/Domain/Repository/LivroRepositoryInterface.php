<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Livro;

interface LivroRepositoryInterface
{
    public function create(Livro $livro): Livro;
    public function findById(string $id): ?Livro;
    public function findAll(): array;
    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
    public function update(Livro $livro): Livro;
    public function delete(string $id): void;
}

