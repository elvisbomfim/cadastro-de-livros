<?php

namespace Core\Domain\Entity;


use Exception;

abstract class Entity
{
    
    public function __get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }

        $className = get_class($this);
        throw new Exception("Propriedade {$property} não encontrada na class {$className}");
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function getId()
    {
        return $this->id;
    }
  
}