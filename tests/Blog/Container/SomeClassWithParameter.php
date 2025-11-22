<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Container;

/**
 * Класс с одним параметром
 */
readonly class SomeClassWithParameter
{

    public function __construct(
        private int $value
    ) {
    }

    public function getValue(): int
    {
        return $this->value;
    }

}
