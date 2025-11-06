<?php

namespace Core\Domain\ValueObject;

class Descricao extends TextValueObject
{
    protected function getErrorMessageEmpty(): string
    {
        return 'Descrição não pode ser vazia.';
    }

    protected function getErrorMessageMaxLength(): string
    {
        return 'Descrição não pode ter mais de %d caracteres.';
    }
}