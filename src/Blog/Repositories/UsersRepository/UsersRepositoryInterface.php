<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\UsersRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\User;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface UsersRepositoryInterface
{

    /**
     * Сохранение пользователя
     * @param User $user
     * @return void
     */
    public function save(User $user): void;

    /**
     * Получение пользователя по его UUID
     * @param UUID $uuid
     * @return User
     */
    public function get(UUID $uuid): User;

    /*
     * Получение пользователя по его логину
     */
    public function getByUsername(string $username): User;

}
