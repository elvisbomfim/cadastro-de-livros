<?php     

namespace Core\Domain\Events;

class LivroCriadoEvent implements EventInterface
{
    public function __construct(
        public protected Livro $livro,
    ) {}

    public function getEventName(): string
    {
        return 'livro.criado';
    }

    public function getPayload(): array
    {
        return ['livro' => $this->livro];
    }
}