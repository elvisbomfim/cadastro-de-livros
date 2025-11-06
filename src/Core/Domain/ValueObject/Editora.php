<?php

namespace Core\Domain\ValueObject;

class Editora extends TextValueObject
{
    protected function getErrorMessageEmpty(): string
    {
        return 'Editora não pode ser vazia.';
    }

    protected function getErrorMessageMaxLength(): string
    {
        return 'Editora não pode ter mais de %d caracteres.';
    }
}