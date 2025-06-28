<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;

class UUID
{

    /**
     * Внутри объекта храним UUID как строку
     * @throws InvalidArgumentException
     */
    public function __construct(private string $uuidString) {
        // Если входная строка не подходит по формату -
        // бросаем исключение InvalidArgumentException
        // Таким образом, гарантируем, что если объект
        // был создан, то он точно содержит правильный UUID
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }

    /**
     * Генерируем новый случайный UUID и получаем его в качестве объекта нашего класса
     * @throws InvalidArgumentException
     */
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

}
