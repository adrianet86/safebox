<?php

namespace SafeBox\Application\Service\SafeBox;


class RetrieveSafeBoxContentRequest
{
    private $id;
    private $token;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function token(): string
    {
        return $this->token;
    }
}