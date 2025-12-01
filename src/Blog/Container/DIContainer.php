<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Container;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Контейнер внедрения зависимостей
 *
 * Реализует контракт ContainerInterface, отписанный в PSR-11
 */
class DIContainer implements ContainerInterface
{

    /**
     * @var array Правила создания объектов
     */
    private array $resolvers = [];

    /**
     * Добавить правило
     *
     * @param string $id Идентификатор искомого объекта
     * @param string|object $resolver Имя класса (строка) или экземпляр класса (объект)
     *
     * @return void
     */
    public function bind(string $id, string|object $resolver): void
    {
        $this->resolvers[$id] = $resolver;
    }

    /**
     * Найти объект по его идентификатору и вернуть его
     *
     * Обязательный метод в PSR-11
     *
     * @param string $id Идентификатор искомого объекта
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function get(string $id): object
    {
        // Если есть правило для создания объекта типа $type
        // (например, $type имеет значение '...\UsersRepositoryInterface')
        if (array_key_exists($id, $this->resolvers)) {
            // Создавать объект того класса, который указан в правиле
            // (например, '...\InMemoryUsersRepository')
            $typeToCreate = $this->resolvers[$id];

            // Если в контейнере для запрашиваемого типа
            // уже есть предопределённый экземпляр класса — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            // Вызвать тот же самый метод контейнера и передаём в него имя класса, указанного в правиле
            return $this->get($typeToCreate);
        }

        // Бросаем исключение, только если класс не существует
        if (!class_exists($id)) {
            throw new NotFoundException("Невозможно определить тип: $id");
        }

        // Создаём объект рефлексии для запрашиваемого класса
        $reflectionClass = new ReflectionClass($id);

        // Исследуем конструктор класса
        $constructor = $reflectionClass->getConstructor();

        // Если конструктора нет - просто создаём объект нужного класса
        if ($constructor === null) {
            // Создаём объект класса $type
            return new $id();
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
        return new $id(...$parameters);
    }

    /**
     * Проверить, может ли объект быть создан контейнером
     *
     * Возвращает true, если контейнер может вернуть объект
     * по этому идентификатору, false – в противном случае
     *
     * Если `has($id)` возвращает true, это не значит,
     * что `get($id)` не выбросит исключения.
     * Это значит, однако, что `get($id)`
     * не выбросит исключения `NotFoundExceptionInterface`
     *
     * Обязательный метод в PSR-11
     *
     * @param string $id Идентификатор искомого объекта
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        try {
            // Пытаемся создать объект требуемого типа
            $this->get($id);
        } catch (NotFoundException) {
            // Возвращаем false, если объект не создан...
            return false;
        }

        // Возвращаем true, если создан
        return true;
    }

}
