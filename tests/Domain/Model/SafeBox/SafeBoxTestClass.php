<?php

namespace Tests\Domain\Model\SafeBox;

use AdsMurai\Domain\SafeBox\SafeBox;

class SafeBoxTestClass extends SafeBox
{
    public function setFailedAttempts(int $failedAttempts)
    {
        $this->failedAttempts = $failedAttempts;
    }

    public function getItemsByPassword(string $password)
    {
        return parent::itemsByToken($this->tokenByPassword($password, 30));
    }

    public function getItems()
    {
        return parent::items();
    }
}