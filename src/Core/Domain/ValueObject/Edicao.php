<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class Edicao
{
    private const MIN_VALUE = 1;

    public function __construct(private int $value)
    {
        $this->validate();
    }

    public function value(): int
    {
        return $this->value;
    }

    private function validate(): void
    {
        if ($this->value < self::MIN_VALUE) {
            throw new InvalidArgumentException('Edição não pode ser menor ou igual a 0.');
        }
    }
}