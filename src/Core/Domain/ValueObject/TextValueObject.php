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
        
        if (empty($this->value)) {
            return;
        }
        
        $preposicoes = ['de', 'da', 'do', 'das', 'dos', 'em', 'na', 'no', 'nas', 'nos', 'a', 'ao', 'aos', 'para', 'por', 'com', 'sem', 'sob', 'sobre', 'entre', 'ante', 'até', 'contra', 'desde', 'perante', 'trás'];
        
        $words = explode(' ', $this->value);
        $normalized = [];
        
        foreach ($words as $index => $word) {
            $wordLower = mb_strtolower($word, 'UTF-8');
            
            $uniqueChars = count_chars($word, 3);
            if (strlen($word) > 0 && strlen($uniqueChars) === 1) {
                $normalized[] = $word;
            } elseif ($index === 0 || !in_array($wordLower, $preposicoes)) {
                $normalized[] = mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
            } else {
                $normalized[] = $wordLower;
            }
        }
        
        $this->value = implode(' ', $normalized);
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

