<?php

namespace Core\Domain\Entity;

use Core\Domain\ValueObject\Uuid;
use Core\Domain\ValueObject\Descricao;
use Core\Domain\Exception\EntityValidationException;

class Assunto extends Entity
{
    public function __construct(
        protected Descricao $descricao,
        protected ?Uuid $id = null,        
    ) {
        $this->id = $this->id ?? Uuid::random();

        $this->validation();
    }

    public function getDescricao(): Descricao
    {
        return $this->descricao;
    }

    protected function validation(): void 
    {
        if ($this->descricao->value() === '') {
            throw new EntityValidationException('Descrição é obrigatória');
        }
    }
}

