<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Repositories\PostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{

    // Функция возвращает объект анонимного класса (в данном случае это стаб),
    // реализующего контракт UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                return new User(
                    new UUID('123e4567-e89b-12d3-a456-426614174001'),
                    new Name('Ivan', 'Petrov'),
                    'Ivan123',
                );
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

        };
    }

    /**
     * Тест проверяет, что SQLite-репозиторий при сохранении статьи сохраняет данные в БД
     * @throws Exception
     */
    public function testItSavesPostToDatabase()
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
                ':user_uuid' => '123e4567-e89b-12d3-a456-426614174001',
                ':title' => 'Заголовок',
                ':text' => 'Текст',
            ]);

        // При вызове метода prepare стаб подключения возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // Передаём в репозиторий стаб подключения
        $repository = new SqlitePostsRepository($connectionStub, $this->makeUsersRepository());

        // Вызываем метод сохранения пользователя
        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new User(
                    new UUID('123e4567-e89b-12d3-a456-426614174001'),
                    new Name('Ivan', 'Petrov'),
                    'ivan123',
                ),
                'Заголовок',
                'Текст',
            )
        );
    }

    /**
     * Тест проверяет, что SQLite-репозиторий бросает исключение, когда запрашиваемая статья не найдена
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
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
        $repository = new SqlitePostsRepository($connectionStub, $this->makeUsersRepository());

        // Ожидаем, что будет брошено исключение
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Статья не найдена: 123e4567-e89b-12d3-a456-426614174000');

        // Вызываем метод получения пользователя
        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }

    /**
     * Тест проверяет, что SQLite-репозиторий возвращает статью, когда запрашиваемая статья найдена по её uuid
     * @throws Exception
     */
    public function testItGetPostByUuidWhenPostFound(): void
    {
        // Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // Создаём стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // Стаб запроса будет возвращать объект статьи из БД при вызове метода fetch
        $statementStub->method('fetch')->willReturn([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'user_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'title' => 'Заголовок',
            'text' => 'Текст',
        ]);

        // Стаб подключения будет возвращать другой стаб (стаб запроса) при вызове метода prepare
        $connectionStub->method('prepare')->willReturn($statementStub);

        // Передаём в репозиторий стаб подключения
        $repository = new SqlitePostsRepository($connectionStub, $this->makeUsersRepository());

        // Вызываем метод получения статьи
        $post = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string)$post->getUuid());
    }

}
