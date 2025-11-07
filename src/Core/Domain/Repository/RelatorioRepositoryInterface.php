<?php

namespace Core\Domain\Repository;

interface RelatorioRepositoryInterface
{
    public function livrosPorCategoria(): array;
    public function fichaDetalhadaLivro(string $id): ?array;
    public function relatorioPorAutor(): array;
}

