<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Container;

use JurisBerkulis\GbPhpL2Hw\Blog\Container\DIContainer;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\NotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\InMemoryUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DIContainerTest extends TestCase
{

    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Описываем ожидаемое исключение
        $this->expectException(NotFoundException::class);

        $this->expectExceptionMessage(
            'Невозможно определить тип: JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Container\SomeClass'
        );

        // Пытаемся получить объект несуществующего класса
        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Пытаемся получить объект класса без зависимостей
        $object = $container->get(SomeClassWithoutDependencies::class);

        // Проверяем, что объект, который вернул контейнер, имеет желаемый тип
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому всякий раз,
        // когда контейнеру нужно создать объект,
        // реализующий контракт UsersRepositoryInterface,
        // он возвращал бы объект класса InMemoryUsersRepository
        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        $container->bind(
            LoggerInterface::class,
            new DummyLogger(),
        );

        // Пытаемся получить объект класса, реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);

        // Проверяем, что контейнер вернул объект класса InMemoryUsersRepository
        $this->assertInstanceOf(
            InMemoryUsersRepository::class,
            $object
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило, по которому всякий раз,
        // когда контейнеру нужно вернуть объект типа SomeClassWithParameter,
        // он возвращал бы предопределённый экземпляр класса SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);

        // Проверяем, что контейнер вернул объект того же типа
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        // Проверяем, что контейнер вернул тот же самый объект
        $this->assertSame(42, $object->getValue());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();

        // Устанавливаем правило получения объекта типа SomeClassWithParameter
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(SomeClassDependingOnAnother::class);

        // Проверяем, что контейнер вернул объект нужного нам типа
        $this->assertInstanceOf(
            SomeClassDependingOnAnother::class,
            $object
        );
    }

}
