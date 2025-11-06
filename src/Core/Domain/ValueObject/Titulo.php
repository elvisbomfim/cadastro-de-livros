<?php

namespace Core\Domain\ValueObject;

class Titulo extends TextValueObject
{
    protected function getErrorMessageEmpty(): string
    {
        return 'Título não pode ser vazio.';
    }

    protected function getErrorMessageMaxLength(): string
    {
        return 'Título não pode ter mais de %d caracteres.';
    }
}