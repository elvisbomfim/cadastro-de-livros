<?php

namespace Core\UseCase\Livro;

use Core\Domain\Entity\Livro;
use Core\Domain\Events\EventDispatcherInterface;
use Core\Domain\Events\LivroCriadoEvent;
use Core\Domain\Repository\LivroRepositoryInterface;
use Core\Domain\ValueObject\Titulo;
use Core\Domain\ValueObject\Editora;
use Core\Domain\ValueObject\Edicao;
use Core\Domain\ValueObject\AnoPublicacao;
use Core\Domain\ValueObject\Moeda;

class CriarLivroUseCase
{
    public function __construct(
        private LivroRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(
        string $titulo,
        string $editora,
        int $edicao,
        int $anoPublicacao,
        float $preco
    ): Livro {
        $livro = new Livro(
            titulo: new Titulo($titulo),
            editora: new Editora($editora),
            edicao: new Edicao($edicao),
            anoPublicacao: new AnoPublicacao($anoPublicacao),
            preco: new Moeda($preco)
        );

        $livroCriado = $this->repository->create($livro);

        // Disparar evento de livro criado
        $this->eventDispatcher->dispatch(new LivroCriadoEvent($livroCriado));

        return $livroCriado;
    }
}

