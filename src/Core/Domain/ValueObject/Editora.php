<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class Editora
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
                sprintf('Editora não pode ter mais de %d caracteres.', self::MAX_LENGTH)
            );
        }
    }
}