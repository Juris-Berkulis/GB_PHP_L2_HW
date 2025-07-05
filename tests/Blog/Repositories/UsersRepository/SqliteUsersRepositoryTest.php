<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{

    /**
     * Тест проверяет, что SQLite-репозиторий бросает исключение, когда запрашиваемый пользователь не найден
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        // Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // Создаём стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // Стаб запроса будет возвращать false при вызове метода fetch
        $statementStub->method('fetch')->willReturn(false);

        // Стаб подключения будет возвращать другой стаб (стаб запроса) при вызове метода prepare
        $connectionStub->method('prepare')->willReturn($statementStub);

        // Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRepository($connectionStub);

        // Ожидаем, что будет брошено исключение
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Пользователь не найден: Ivan');

        // Вызываем метод получения пользователя
        $repository->getByUsername('Ivan');
    }

}
