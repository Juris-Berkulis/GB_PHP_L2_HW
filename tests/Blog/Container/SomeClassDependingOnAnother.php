<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Container;

/**
 * Класс с двумя зависимостями
 */
class SomeClassDependingOnAnother
{

    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    )
    {
    }

}
