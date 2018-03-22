<?php

namespace SafeBox\Application\Service\SafeBox;


class PasswordValidator
{
    const MINIMUM_LENGTH = 6;

    /**
     * PasswordValidator constructor.
     * @param CommonPasswordRepositoryInterface $commonPasswordRepository
     */
    public function __construct(CommonPasswordRepositoryInterface $commonPasswordRepository)
    {
        $this->commonPasswordRepository = $commonPasswordRepository;
    }

    public function validateStrength(string $password)
    {
        $this->largeEnough($password);
        $this->notCommon($password);
    }

    /**
     * @param $password
     * @throws TooShortPasswordException
     */
    public function largeEnough(string $password)
    {
        if (strlen($password) < 6) {
            throw new TooShortPasswordException('Password must contain at least 6 characters');
        }
    }

    /**
     * @param string $password
     * @throws CommonPasswordException
     */
    public function notCommon(string $password)
    {
        if (in_array($password, $this->commonPasswordRepository->all())) {
            throw new CommonPasswordException('Password is too common');
        }
    }

}