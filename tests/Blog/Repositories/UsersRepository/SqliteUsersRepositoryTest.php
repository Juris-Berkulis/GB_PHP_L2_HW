<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use JurisBerkulis\GbPhpL2Hw\UnitTests\DummyLogger;
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
        $repository = new SqliteUsersRepository(
            $connectionStub,
            new DummyLogger(),
        );

        // Ожидаем, что будет брошено исключение
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Пользователь не найден: Ivan');

        // Вызываем метод получения пользователя
        $repository->getByUsername('Ivan');
    }

    /**
     * Тест проверяет, что SQLite-репозиторий при сохранении пользователя сохраняет данные в БД
     * @throws Exception
     */
    public function testItSavesUserToDatabase(): void
    {
        // Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);

        // Описываем ожидаемое взаимодействие репозитория с моком запроса
        $statementMock
            // Ожидаем, что будет вызван один раз
            ->expects($this->once())
            // метод execute
            ->method('execute')
            // с единственным аргументом - массивом
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':password' => 'some_password',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
            ]);

        // При вызове метода prepare стаб подключения возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRepository(
            $connectionStub,
            new DummyLogger(),
        );

        // Вызываем метод сохранения пользователя
        $repository->save(
            new User(
                // Свойства пользователя точно такие, как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123',
                'some_password',
            ),
        );
    }

}
