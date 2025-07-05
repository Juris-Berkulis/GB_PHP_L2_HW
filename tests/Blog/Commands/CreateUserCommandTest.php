<?php

namespace JurisBerkulis\GbPhpL2Hw\UnitTests\Blog\Commands;

use JurisBerkulis\GbPhpL2Hw\Blog\Commands\Arguments;
use JurisBerkulis\GbPhpL2Hw\Blog\Commands\CreateUserCommand;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\ArgumentsException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\CommandException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\InvalidArgumentException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\UserNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\DummyUsersRepository;
use JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{

    /**
     * Тест проверяет, что команда создания пользователя бросает исключение, если пользователь с таким именем уже существует
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(
            // Передаём стаб в качестве реализации UsersRepositoryInterface
            new DummyUsersRepository(),
        );

        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);

        // и его сообщение
        $this->expectExceptionMessage('Пользователь уже существует: Ivan');

        // Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

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
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Нет такого аргумента: first_name');
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    /**
     * Тест проверяет, что команда создания пользователя действительно требует фамилию пользователя
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Нет такого аргумента: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            // Передаём имя пользователя, чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
    }

    /**
     * Тест проверяет, что команда создания пользователя сохраняет пользователя в репозитории
     * @throws CommandException
     * @throws ArgumentsException
     * @throws InvalidArgumentException
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
        $command = new CreateUserCommand($usersRepository);

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        // Проверяем утверждение относительно мока, а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

}
