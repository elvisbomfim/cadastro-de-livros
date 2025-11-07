<?php

namespace Core\UseCase\Relatorio;

use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\Repository\RelatorioRepositoryInterface;

class FichaDetalhadaLivroUseCase
{
    public function __construct(
        private RelatorioRepositoryInterface $repository
    ) {}

    public function execute(string $id): array
    {
        $ficha = $this->repository->fichaDetalhadaLivro($id);
        
        if (empty($ficha)) {
            throw new LivroNaoEncontradoException("Livro com ID {$id} não encontrado.");
        }
        
        return $ficha;
    }
}

