<?php

namespace SafeBox\Application\Service\SafeBox;

use SafeBox\Domain\SafeBox\SafeBox;
use SafeBox\Domain\SafeBox\SafeBoxExistsException;
use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;

class CreateNewSafeBoxService
{
    /**
     * @var SafeBoxRepositoryInterface
     */
    private $safeBoxRepository;
    /**
     * @var PasswordValidator
     */
    private $passwordValidator;

    /**
     * CreateNewSafeBoxService constructor.
     * @param SafeBoxRepositoryInterface $safeBoxRepository
     * @param CommonPasswordRepositoryInterface $commonPasswordRepository
     */
    public function __construct(
        SafeBoxRepositoryInterface $safeBoxRepository,
        CommonPasswordRepositoryInterface $commonPasswordRepository
    )
    {
        $this->safeBoxRepository = $safeBoxRepository;
        $this->passwordValidator = new PasswordValidator($commonPasswordRepository);
    }

    /**
     * @param null $request
     * @return SafeBox
     * @throws SafeBoxExistsException
     */
    public function execute($request = null)
    {
        $name = $request->name();
        $password = $request->password();

        $this->passwordValidator->validateStrength($password);

        if (!empty($this->safeBoxRepository->byName($name))) {
            throw new SafeBoxExistsException("Safebox already exists with this name: $name");
        }

        $safeBox = new SafeBox($name, $password);

        $this->safeBoxRepository->add($safeBox);

        return $safeBox;
    }
}