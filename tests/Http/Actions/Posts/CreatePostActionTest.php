<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Http\Actions\Posts;

use JsonException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\PostNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Posts\CreatePost;
use JurisBerkulis\GbPhpL2Hw\Http\Auth\IdentificationInterface;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use JurisBerkulis\GbPhpL2Hw\UnitTests\DummyLogger;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{

    private function assertValidUuid(string $uuid): void
    {
        try {
            new UUID($uuid);

            // Увеличиваем счетчик утверждений (UUID валиден)
            $this->addToAssertionCount(1);
        } catch (InvalidArgumentException $e) {
            $this->fail("Неверный UUID: $uuid - " . $e->getMessage());
        }
    }

    public function usersRepository(array $users): UsersRepositoryInterface
    {
        return new readonly class($users) implements UsersRepositoryInterface
        {

            public function __construct(private array $users)
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$user->getUuid() === (string)$uuid) return $user;
                }

                throw new UserNotFoundException("Пользователь не найден: $uuid");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $user->getUsername() === $username) return $user;
                }

                throw new UserNotFoundException("Пользователь не найден: $username");
            }
        };
    }

    public function identification(
        UsersRepositoryInterface $usersRepository,
        string $username,
    ): IdentificationInterface
    {
        return new readonly class (
            $usersRepository,
            $username,
        ) implements IdentificationInterface
        {

            public function __construct(
                private UsersRepositoryInterface $usersRepository,
                private string $username,
            )
            {
            }

            public function getUserByUuid(Request $request): User
            {
                throw new AuthException('');
            }

            public function getUserByUsername(Request $request): User
            {
                return $this->usersRepository->getByUsername($this->username);
            }
        };
    }

    public function postsRepository(): PostsRepositoryInterface
    {
        return new class() implements PostsRepositoryInterface
        {

            public function __construct()
            {
            }

            public function save(Post $post): void
            {
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException();
            }

            public function delete(UUID $uuid): void
            {
            }

        };
    }

    /**
     * Тест, проверяющий, что будет возвращён удачный ответ
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws InvalidArgumentException|JsonException
     * @throws AuthException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsSuccessfulResponse(): void
    {
        $authorUuid = '4fcfce3d-10ae-4f9d-8911-c3e156aa957a';
        $username = 'ivan';

        $request = new Request([], [], '{"username":"' . $username . '","text":"some text","title":"some title"}');

        $users = [
            new User(
                new UUID($authorUuid),
                new Name('Ivan', 'Petrov'),
                $username,
            ),
        ];

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository($users);
        $identification = $this->identification($usersRepository, $username);

        $action = new CreatePost(
            $postsRepository,
            $identification,
            new DummyLogger(),
        );

        $response = $action->handle($request);

        // Захватываем вывод
        ob_start();
        $response->send();
        $output = ob_get_clean();

        // Отладочный вывод
        echo "Ответ: " . $output . "\n";

        // Парсим JSON и проверяем структуру
        $responseData = json_decode($output, true);

        // Проверки
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('uuid', $responseData['data']);
        $this->assertValidUuid($responseData['data']['uuid']);
    }

    /**
     * Тест, проверяющий, что будет возвращён неудачный ответ, если пользователь не найден
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws JsonException|InvalidArgumentException
     * @throws AuthException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $username = 'ivan';

        $request = new Request([], [], '{"username":"' . $username . '","text":"some text","title":"some title"}');

        // Нет пользователей
        $users = [];

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository($users);
        $identification = $this->identification($usersRepository, $username);

        $action = new CreatePost(
            $postsRepository,
            $identification,
            new DummyLogger(),
        );

        $response = $action->handle($request);

        // Захватываем вывод
        ob_start();
        $response->send();
        $output = ob_get_clean();

        // Отладочный вывод
        echo "Ответ: " . $output . "\n";

        // Парсим JSON и проверяем структуру
        $responseData = json_decode($output, true);

        // Проверки
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('reason', $responseData);
        $this->assertTrue($responseData['reason'] === "Пользователь не найден: $username");
    }

    /**
     * Тест, проверяющий, что будет возвращён неудачный ответ, если нет параметра text
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws JsonException|InvalidArgumentException
     * @throws AuthException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsErrorResponseIfNoTextProvided(): void
    {
        $authorUuid = '4fcfce3d-10ae-4f9d-8911-c3e156aa957a';
        $username = 'ivan';

        $request = new Request([], [], '{"username":"' . $username . '","title":"some title"}');

        $users = [
            new User(
                new UUID($authorUuid),
                new Name('Ivan', 'Petrov'),
                $username,
            ),
        ];

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository($users);
        $identification = $this->identification($usersRepository, $username);

        $action = new CreatePost(
            $postsRepository,
            $identification,
            new DummyLogger(),
        );

        $response = $action->handle($request);

        // Захватываем вывод
        ob_start();
        $response->send();
        $output = ob_get_clean();

        // Отладочный вывод
        echo "Ответ: " . $output . "\n";

        // Парсим JSON и проверяем структуру
        $responseData = json_decode($output, true);

        // Проверки
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('reason', $responseData);
        $this->assertTrue($responseData['reason'] === 'Нет поля: text');
    }

    /**
     * Тест, проверяющий, что будет возвращён неудачный ответ, если параметр title пустой
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws InvalidArgumentException|JsonException
     * @throws AuthException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsErrorResponseIfTitleIsEmpty(): void
    {
        $authorUuid = '4fcfce3d-10ae-4f9d-8911-c3e156aa957a';
        $username = 'ivan';

        $request = new Request([], [], '{"username":"' . $username . '","text":"some text","title":""}');

        $users = [
            new User(
                new UUID($authorUuid),
                new Name('Ivan', 'Petrov'),
                $username,
            ),
        ];

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository($users);
        $identification = $this->identification($usersRepository, $username);

        $action = new CreatePost(
            $postsRepository,
            $identification,
            new DummyLogger(),
        );

        $response = $action->handle($request);

        // Захватываем вывод
        ob_start();
        $response->send();
        $output = ob_get_clean();

        // Отладочный вывод
        echo "Ответ: " . $output . "\n";

        // Парсим JSON и проверяем структуру
        $responseData = json_decode($output, true);

        // Проверки
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('reason', $responseData);
        $this->assertTrue($responseData['reason'] === "Пустое поле: title");
    }

}
