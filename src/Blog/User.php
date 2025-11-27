<?php
namespace JurisBerkulis\GbPhpL2Hw\Blog;

use JurisBerkulis\GbPhpL2Hw\Person\Name;

class User
{

    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $password;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $login
     * @param string $password
     */
    public function __construct(UUID $uuid, Name $name, string $login, string $password)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $login;
        $this->password = $password;
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
    public function getPassword(): string
    {
        return $this->password;
    }

}
