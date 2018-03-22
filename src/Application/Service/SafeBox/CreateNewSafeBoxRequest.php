<?php

namespace SafeBox\Application\Service\SafeBox;


class CreateNewSafeBoxRequest
{
    private $name;
    private $password;

    public function __construct(string $name, string $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): string
    {
        return $this->password;
    }
}