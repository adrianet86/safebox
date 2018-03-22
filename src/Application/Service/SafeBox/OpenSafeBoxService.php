<?php

namespace SafeBox\Application\Service\SafeBox;

use SafeBox\Domain\SafeBox\SafeBoxBlockedException;
use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;
use SafeBox\Domain\SafeBox\WrongPasswordException;

class OpenSafeBoxService
{
    /**
     * @var SafeBoxRepositoryInterface
     */
    private $safeBoxRepository;

    /**
     * @param SafeBoxRepositoryInterface $safeBoxRepository
     */
    public function __construct(SafeBoxRepositoryInterface $safeBoxRepository)
    {
        $this->safeBoxRepository = $safeBoxRepository;
    }

    /**
     * @param null $request
     * @return string
     * @throws WrongPasswordException
     * @throws \Exception
     */
    public function execute($request = null)
    {
        try {
            $id = $request->id();
            $password = $request->password();
            $expiration = $request->expiration();
            $safeBox = $this->safeBoxRepository->byIdOrFail($id);

            return $safeBox->tokenByPassword($password, $expiration);

        } catch (WrongPasswordException $exception) {
            $this->safeBoxRepository->store($safeBox);//update for failed attempts
            throw $exception;
        } catch (SafeBoxBlockedException $exception) {
            $this->safeBoxRepository->store($safeBox);//update for failed attempts
            throw $exception;
        }
    }
}