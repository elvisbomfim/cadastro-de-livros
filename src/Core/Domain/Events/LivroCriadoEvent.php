<?php     

namespace Core\Domain\Events;

use Core\Domain\Entity\Livro;

class LivroCriadoEvent implements EventInterface
{
    public function __construct(
        protected Livro $livro,
    ) {}

    public function getEventName(): string
    {
        return 'livro.criado';
    }

    public function getPayload(): array
    {
        return ['livro' => $this->livro];
    }

    public function getLivro(): Livro
    {
        return $this->livro;
    }
}