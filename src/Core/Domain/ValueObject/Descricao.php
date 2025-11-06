<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class Descricao
{
    private const MAX_LENGTH = 40; 

    public function __construct(private string $value)
    {
        $this->validate();
    }

    public function value(): string
    {
        return $this->value;
    }

    private function validate(): void
    {
        if (strlen($this->value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Descrição não pode ter mais de %d caracteres.', self::MAX_LENGTH)
            );
        }
    }
}