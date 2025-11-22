<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Container;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\NotFoundException;

class DIContainer
{

    // Правила создания объектов
    private array $resolvers = [];

    /**
     * Добавить правило
     * @param string $type
     * @param string $class
     * @return void
     */
    public function bind(string $type, string $class): void
    {
        $this->resolvers[$type] = $class;
    }

    public function get(string $type): object
    {
        // Если есть правило для создания объекта типа $type
        // (например, $type имеет значение '...\UsersRepositoryInterface')
        if (array_key_exists($type, $this->resolvers)) {
            // Создавать объект того класса, который указан в правиле
            // (например, '...\InMemoryUsersRepository')
            $typeToCreate = $this->resolvers[$type];

            // Вызвать тот же самый метод контейнера и передаём в него имя класса, указанного в правиле
            return $this->get($typeToCreate);
        }

        // Бросаем исключение, только если класс не существует
        if (!class_exists($type)) {
            throw new NotFoundException("Невозможно определить тип: $type");
        }

        // Создаём объект класса $type
        return new $type();
    }

}
