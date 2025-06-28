<?php
namespace JurisBerkulis\GbPhpL2Hw\Blog;

use JurisBerkulis\GbPhpL2Hw\Person\Name;

class User
{

    private UUID $uuid;
    private Name $name;
    private string $username;

    /**
     * @param UUID $uuid
     * @param Name $username
     * @param string $login
     */
    public function __construct(UUID $uuid, Name $username, string $login)
    {
        $this->uuid = $uuid;
        $this->name = $username;
        $this->username = $login;
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
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

}