<?php

namespace Blog\Commands\Users;

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Users\CreateUser;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Тесты консольной команды создания пользователя (с помощью пакета "symfony/console")
 *
 * Аналогичны тестам из CreateUserCommandTest.php
 *
 * @see CreateUserCommandTest
 */
class CreateUserTest extends TestCase
{

    /**
     * Вернуть объект анонимного класса (в данном случае это стаб),
     * реализующего контракт UsersRepositoryInterface
     * @return UsersRepositoryInterface
     */
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

        };
    }

    /**
     * Тест проверяет, что команда создания пользователя действительно требует имя пользователя
     * @throws ExceptionInterface
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    /**
     * Тест проверяет, что команда создания пользователя действительно требует фамилию пользователя
     * @throws ExceptionInterface
     */
    public function testItRequiresLastName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );

        // Запускаем команду методом run вместо handle в CreateUserCommandTest.php
        $command->run(
            // Передаём аргументы как ArrayInput, а не Arguments
            // Сами аргументы не меняются
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            // Передаём также объект, реализующий контракт OutputInterface
            // Подойдёт реализация, которая ничего не делает
            new NullOutput()
        );
    }

    /**
     * Тест проверяет, что команда создания пользователя сохраняет пользователя в репозитории
     * @throws ExceptionInterface
     */
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса (в данном случае это мок)
        $usersRepository = new class implements UsersRepositoryInterface {

            // В этом свойстве храним информацию о том, был ли вызван метод save
            private bool $isSaveCalled = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->isSaveCalled = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }

            // С помощью этого метода можем узнать, был ли вызван метод save
            // (этого метода нет в контракте UsersRepositoryInterface)
            public function wasCalled(): bool
            {
                return $this->isSaveCalled;
            }

        };

        // Передаём мок в команду
        $command = new CreateUser(
            $usersRepository
        );

        // Запускаем команду
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        // Проверяем утверждение относительно мока, а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

    /**
     * Тест проверяющий, что аргумент password является обязательным
     * @throws ExceptionInterface
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password"'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

}
