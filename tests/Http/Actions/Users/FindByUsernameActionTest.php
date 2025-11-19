<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Http\Actions\Users;

use JsonException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use JurisBerkulis\GbPhpL2Hw\Http\Actions\Users\FindByUsername;
use JurisBerkulis\GbPhpL2Hw\Http\ErrorResponse;
use JurisBerkulis\GbPhpL2Hw\Http\Request;
use JurisBerkulis\GbPhpL2Hw\Http\SuccessfulResponse;
use JurisBerkulis\GbPhpL2Hw\Person\Name;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{

    /**
     * Тест, проверяющий, что будет возвращён неудачный ответ, если в запросе нет параметра username
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws JsonException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        // Создаём объект запроса
        // Вместо суперглобальных переменных передаём простые массивы
        $request = new Request([], []);

        // Создаём стаб репозитория пользователей
        $usersRepository = $this->usersRepository([]);

        //Создаём объект действия
        $action = new FindByUsername($usersRepository);

        // Запускаем действие
        $response = $action->handle($request);

        // Проверяем, что ответ - неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);

        // Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString('{"success":false,"reason":"В запросе нет такого параметра запроса: username"}');

        // Отправляем ответ в поток вывода
        $response->send();
    }

    /**
     * Тест, проверяющий, что будет возвращён неудачный ответ, если пользователь не найден
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws JsonException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        // Теперь запрос будет иметь параметр username
        $request = new Request(['username' => 'ivan'], []);

        // Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Пользователь не найден"}');

        $response->send();
    }

    /**
     * Тест, проверяющий, что будет возвращён удачный ответ, если пользователь найден
     * @description Запускаем тест (с помощбю RunInSeparateProcess и PreserveGlobalState) в отдельном процессе
     * @throws JsonException|InvalidArgumentException
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], []);

        // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Petrov'),
                'ivan',
            ),
        ]);

        $action = new FindByUsername($usersRepository);
        $response = $action->handle($request);

        // Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Petrov"}}');

        $response->send();
    }

    /**
     * Функция, создающая стаб репозитория пользователей, принимает массив "существующих" пользователей
     * @param array $users
     * @return UsersRepositoryInterface
     */
    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new readonly class($users) implements UsersRepositoryInterface {

            // В конструктор анонимного класса передаём массив пользователей
            public function __construct(
                private array $users
            ) {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Пользователь не найден");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getUsername()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("Пользователь не найден");
            }

        };
    }

}
