<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class Moeda
{
    private const MIN_VALUE = 0;

    public function __construct(private float $value)
    {
        $this->validate();
    }

    public function value(): float
    {
        return $this->value;
    }

    public function formatar(): string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    private function validate(): void
    {
        if ($this->value < self::MIN_VALUE) {
            throw new InvalidArgumentException(
                sprintf('Moeda não pode ser menor que R$ %.2f.', self::MIN_VALUE)
            );
        }
    }
}

