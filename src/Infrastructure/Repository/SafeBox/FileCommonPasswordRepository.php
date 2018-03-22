<?php

namespace SafeBox\Infrastructure\Repository\SafeBox;


use SafeBox\Application\Service\SafeBox\CommonPasswordRepositoryInterface;

class FileCommonPasswordRepository implements CommonPasswordRepositoryInterface
{

    function all(): array
    {
        return [
            '123456',
            '123456789',
            '111111',
            'password',
            'qwerty',
            'abc123',
            '12345678',
            'password1',
            '1234567',
            '123123',
            '1234567890',
            '000000',
            '12345',
            'iloveyou',
            '1q2w3e4r5t',
            '1234',
            '123456a',
            'qwertyuiop',
            'monkey',
            '123321',
            'dragon',
            '654321',
            '666666',
            '123',
            'myspace1',
            'a123456',
            '121212',
            '1qaz2wsx',
            '123qwe',
            '123abc',
            'tinkle',
            'target123',
            'gwerty',
            '1g2w3e4r',
            'gwerty123',
            'zag12wsx',
        ];
    }
}