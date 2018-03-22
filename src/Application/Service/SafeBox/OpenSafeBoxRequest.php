<?php

namespace SafeBox\Application\Service\SafeBox;


class OpenSafeBoxRequest
{
    const EXPIRATION = 180;

    private $id;
    private $password;
    private $expiration;

    public function __construct(string $id, string $password, int $expiration = self::EXPIRATION)
    {
        if (!is_int($expiration) || $expiration <= 0) {
            throw new \Exception('Expiration must be greather than 0');
        }
        $this->id = $id;
        $this->password = $password;
        $this->expiration = $expiration;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function expiration(): int
    {
        return $this->expiration;
    }


}