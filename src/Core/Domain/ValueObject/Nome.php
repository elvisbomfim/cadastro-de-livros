<?php

namespace Core\Domain\ValueObject;

class Nome extends TextValueObject
{
    protected function getErrorMessageEmpty(): string
    {
        return 'Nome não pode ser vazio.';
    }

    protected function getErrorMessageMaxLength(): string
    {
        return 'Nome não pode ter mais de %d caracteres.';
    }
}