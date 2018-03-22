<?php

namespace SafeBox\Application\Service\SafeBox;


use SafeBox\Domain\SafeBox\SafeBoxRepositoryInterface;

class RetrieveSafeBoxContentService
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
        $id = $request->id();
        $token = $request->token();

        $safeBox = $this->safeBoxRepository->byIdOrFail($id);

        return $safeBox->itemsByToken($token);
    }
}