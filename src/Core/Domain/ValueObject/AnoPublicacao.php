<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class AnoPublicacao
{
    private const MIN_YEAR = 1000;

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
        
        if ($this->value < self::MIN_YEAR) {
            throw new InvalidArgumentException(
                sprintf('Ano de publicação não pode ser menor que %d.', self::MIN_YEAR)
            );
        }
    }
}