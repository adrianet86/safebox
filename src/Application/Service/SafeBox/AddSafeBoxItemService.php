<?php

namespace SafeBox\Application\Service\SafeBox;

use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;

class AddSafeBoxItemService
{
    /**
     * @var SafeBoxRepositoryInterface
     */
    private $safeBoxRepository;

    public function __construct(SafeBoxRepositoryInterface $safeBoxRepository)
    {
        $this->safeBoxRepository = $safeBoxRepository;
    }


    public function execute($request = null)
    {
        $safeBox = $this->safeBoxRepository->byIdOrFail($request->id());

        $safeBox->validateToken($request->token());

        $safeBox->addItem($request->item());

        $this->safeBoxRepository->store($safeBox);
    }
}