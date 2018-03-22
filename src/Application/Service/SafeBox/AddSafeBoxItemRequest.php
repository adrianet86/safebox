<?php

namespace SafeBox\Application\Service\SafeBox;


class AddSafeBoxItemRequest
{
    private $id;
    private $token;
    private $item;

    public function __construct(string $id, string $token, string $item)
    {
        $this->id = $id;
        $this->token = $token;
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function token(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function item(): string
    {
        return $this->item;
    }

}