<?php

namespace Core\Domain\Entity;

use Core\Domain\ValueObject\Uuid;
use Core\Domain\ValueObject\Nome;
use Core\Domain\Exception\EntityValidationException;

class Autor extends Entity
{
    public function __construct(
        protected Nome $nome,
        protected ?Uuid $id = null,        
    ) {
        $this->id = $this->id ?? Uuid::random();

        $this->validation();
    }

    public function getNome(): Nome
    {
        return $this->nome;
    }

    protected function validation(): void 
    {
        if ($this->nome->value() === '') {
            throw new EntityValidationException('Nome é obrigatório');
        }
    }
}

