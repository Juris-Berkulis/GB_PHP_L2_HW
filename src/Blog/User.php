<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

use JurisBerkulis\GbPhpL2Hw\Person\Name;

class User
{

    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $hashedPassword;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $login
     * @param string $hashedPassword
     */
    public function __construct(UUID $uuid, Name $name, string $login, string $hashedPassword)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $login;
        $this->hashedPassword = $hashedPassword;
    }

    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name и логином $this->username";
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * Вычислить хеша пароля
     *
     * Использует алгоритм хеширования SHA-256
     *
     * uuid применяется в качестве соли
     *
     * @param UUID $uuid
     * @param string $password
     * @return string
     */
    private static function hash(UUID $uuid, string $password): string
    {
        // Используем UUID в качестве соли
        return hash('sha256', $uuid . $password);
    }

    /**
     * Проверить предъявленный пароль
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($this->uuid, $password);
    }

    /**
     * Создать нового пользователя
     *
     * Создаёт UUID новому пользователю и хеширует его пароль
     *
     * @param string $username
     * @param string $password
     * @param Name $name
     * @return self
     * @throws Exceptions\InvalidArgumentException
     */
    public static function createFrom(
        string $username,
        string $password,
        Name   $name
    ): self
    {
        $uuid = UUID::random();

        return new self(
            $uuid,
            $name,
            $username,
            self::hash($uuid, $password),
        );
    }

}
