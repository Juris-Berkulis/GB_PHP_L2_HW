<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\AuthTokensRepository;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use JurisBerkulis\GbPhpL2Hw\Blog\AuthToken;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokenNotFoundException;
use JurisBerkulis\GbPhpL2Hw\Blog\Exceptions\AuthTokensRepositoryException;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;
use PDO;
use PDOException;

readonly class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{

    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                user_uuid,
                expires_on
            )
            VALUES (
                :token,
                :user_uuid,
                :expires_on
            )
            ON CONFLICT (token)
            DO UPDATE SET expires_on = :expires_on
        SQL;

        try {
            $statement = $this->connection->prepare($query);

            $statement->execute([
                ':token' => $authToken->getToken(),
                ':user_uuid' => (string)$authToken->getUserUuid(),
                ':expires_on' => $authToken->getExpiresOn()->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );

            $statement->execute([$token]);

            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
        }

        if ($result === false) {
            throw new AuthTokenNotFoundException("Токен не найден: $token");
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
}
