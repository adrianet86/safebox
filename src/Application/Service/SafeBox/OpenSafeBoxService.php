<?php

namespace AdsMurai\Application\Service\SafeBox;

use AdsMurai\Domain\SafeBox\SafeBoxBlockedException;
use AdsMurai\Domain\SafeBox\SafeBoxRepositoryInterface;
use AdsMurai\Domain\SafeBox\WrongPasswordException;

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