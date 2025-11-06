<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

abstract class TextValueObject
{
    protected const MAX_LENGTH = 40;

    public function __construct(protected string $value)
    {
        $this->normalize();
        $this->validate();
    }

    public function value(): string
    {
        return $this->value;
    }

    protected function normalize(): void
    {
        $this->value = trim($this->value);
        $this->value = mb_convert_case($this->value, MB_CASE_TITLE, 'UTF-8');
    }

    protected function validate(): void
    {
        if (strlen($this->value) === 0) {
            throw new InvalidArgumentException($this->getErrorMessageEmpty());
        }

        if (strlen($this->value) > static::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf($this->getErrorMessageMaxLength(), static::MAX_LENGTH)
            );
        }
    }

    abstract protected function getErrorMessageEmpty(): string;
    abstract protected function getErrorMessageMaxLength(): string;
}

