<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Repositories\CommandsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommentNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommandsRepository\SqliteCommandsRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SqliteCommandsRepositoryTest extends TestCase
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
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
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

    // Функция возвращает объект анонимного класса (в данном случае это стаб),
    // реализующего контракт UsersRepositoryInterface
    private function makePostsRepository(): PostsRepositoryInterface
    {
        return new class implements PostsRepositoryInterface {

            public function save(Post $post): void
            {
            }

            public function get(UUID $uuid): Post
            {
                return new Post(
                    new UUID('123e4567-e89b-12d3-a456-426614174001'),
                    new User(
                        new UUID('123e4567-e89b-12d3-a456-426614174000'),
                        new Name('Ivan', 'Petrov'),
                        'Ivan123',
                    ),
                    'Заголовок',
                    'Тект комментария'
                );
            }

            public function getByUsername(string $username): User
            {
                throw new PostNotFoundException("Not found");
            }

        };
    }

    /**
     * Тест проверяет, что SQLite-репозиторий при сохранении комментария сохраняет данные в БД
     * @throws Exception
     */
    public function testItSavesCommentToDatabase()
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
                ':uuid' => '123e4567-e89b-12d3-a456-426614174002',
                ':post_uuid' => '123e4567-e89b-12d3-a456-426614174001',
                ':user_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':text' => 'Текст комментария',
            ]);

        // При вызове метода prepare стаб подключения возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // Передаём в репозиторий стаб подключения
        $repository = new SqliteCommandsRepository(
            $connectionStub,
            $this->makePostsRepository(),
            $this->makeUsersRepository(),
        );

        // Вызываем метод сохранения комментария
        $repository->save(
            new Comment(
                new UUID('123e4567-e89b-12d3-a456-426614174002'),
                new User(
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
                    new Name('Ivan', 'Petrov'),
                    'ivan123',
                ),
                new Post(
                    new UUID('123e4567-e89b-12d3-a456-426614174001'),
                    new User(
                        new UUID('123e4567-e89b-12d3-a456-426614174000'),
                        new Name('Ivan', 'Petrov'),
                        'ivan123',
                    ),
                    'Заголовок',
                    'Текст статьи',
                ),
                'Текст комментария',
            ),
        );
    }

    /**
     * Тест проверяет, что SQLite-репозиторий бросает исключение, когда запрашиваемый комментарий не найден
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
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
        $repository = new SqliteCommandsRepository(
            $connectionStub,
            $this->makePostsRepository(),
            $this->makeUsersRepository(),
        );

        // Ожидаем, что будет брошено исключение
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Комментарий не найден: 123e4567-e89b-12d3-a456-426614174002');

        // Вызываем метод получения комментария
        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174002'));
    }

    /**
     * Тест проверяет, что SQLite-репозиторий возвращает комментарий, когда запрашиваемый комментарий найден по его uuid
     * @throws Exception
     */
    public function testItGetCommentByUuidWhenCommentFound(): void
    {
        // Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // Создаём стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);

        // Стаб запроса будет возвращать объект статьи из БД при вызове метода fetch
        $statementStub->method('fetch')->willReturn([
            'uuid' => '123e4567-e89b-12d3-a456-426614174002',
            'post_uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'user_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'text' => 'Текст комментария',
        ]);

        // Стаб подключения будет возвращать другой стаб (стаб запроса) при вызове метода prepare
        $connectionStub->method('prepare')->willReturn($statementStub);

        // Передаём в репозиторий стаб подключения
        $repository = new SqliteCommandsRepository(
            $connectionStub,
            $this->makePostsRepository(),
            $this->makeUsersRepository(),
        );

        // Вызываем метод получения комментария
        $comment = $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174002'));

        $this->assertSame('123e4567-e89b-12d3-a456-426614174002', (string)$comment->getUuid());
    }

}
