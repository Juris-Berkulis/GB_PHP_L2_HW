<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Container;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\NotFoundException;
use ReflectionClass;

class DIContainer
{

    // Правила создания объектов
    private array $resolvers = [];

    /**
     * Добавить правило
     * @param string $type
     * @param string|object $resolver - Имя класса (строка) или экземпляр класса (объект)
     * @return void
     */
    public function bind(string $type, string | object $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    public function get(string $type): object
    {
        // Если есть правило для создания объекта типа $type
        // (например, $type имеет значение '...\UsersRepositoryInterface')
        if (array_key_exists($type, $this->resolvers)) {
            // Создавать объект того класса, который указан в правиле
            // (например, '...\InMemoryUsersRepository')
            $typeToCreate = $this->resolvers[$type];

            // Если в контейнере для запрашиваемого типа
            // уже есть предопределённый экземпляр класса — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            // Вызвать тот же самый метод контейнера и передаём в него имя класса, указанного в правиле
            return $this->get($typeToCreate);
        }

        // Бросаем исключение, только если класс не существует
        if (!class_exists($type)) {
            throw new NotFoundException("Невозможно определить тип: $type");
        }

        // Создаём объект рефлексии для запрашиваемого класса
        $reflectionClass = new ReflectionClass($type);

        // Исследуем конструктор класса
        $constructor = $reflectionClass->getConstructor();

        // Если конструктора нет - просто создаём объект нужного класса
        if ($constructor === null) {
            // Создаём объект класса $type
            return new $type();
        }

        /**
         * Объекты зависимостей класса
         */
        $parameters = [];

        // Проходим по всем параметрам конструктора (зависимостям класса)
        foreach ($constructor->getParameters() as $parameter) {
            // Узнаем тип параметра конструктора (тип зависимости)
            $parameterType = $parameter->getType()->getName();

            // Получаем объект зависимости из контейнера
            $parameters[] = $this->get($parameterType);
        }

        // Создаём объект класса $type с параметрами
        return new $type(...$parameters);
    }

}
