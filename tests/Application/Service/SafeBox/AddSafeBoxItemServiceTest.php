<?php

namespace Tests\Application\Service\SafeBox;

use SafeBox\Application\Service\SafeBox\AddSafeBoxItemRequest;
use SafeBox\Application\Service\SafeBox\AddSafeBoxItemService;
use SafeBox\Domain\SafeBox\SafeBox;

use SafeBox\Infrastructure\Repository\SafeBox\MemorySafeBoxRepository;
use Tests\TestCase;

class AddSafeBoxItemServiceTest extends TestCase
{
    /**
     * @var MemorySafeBoxRepository
     */
    private $safeBoxRepository;
    /**
     * @var AddSafeBoxItemService
     */
    private $addSafeBoxItemService;

    public function setUp()
    {
        parent::setUp();

        $this->safeBoxRepository = new MemorySafeBoxRepository;
        $this->addSafeBoxItemService = new AddSafeBoxItemService($this->safeBoxRepository);
    }

    public function test_wrong_id_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\SafeBoxNotExistsException::class);
        $request = new AddSafeBoxItemRequest('not_exists_id', 'invalid_token', '');

        return $this->addSafeBoxItemService->execute($request);
    }

    public function test_wrong_token_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\InvalidSafeBoxTokenException::class);
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $this->safeBoxRepository->add($safeBox);

        $request = new AddSafeBoxItemRequest($safeBox->id(), 'invalid_token', '');

        return $this->addSafeBoxItemService->execute($request);
    }

    public function test_empty_item_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $password = '¡Strong_Password!';
        $safeBox = new SafeBox('name', $password);
        $this->safeBoxRepository->add($safeBox);
        $token = $safeBox->tokenByPassword($password, 30);
        $request = new AddSafeBoxItemRequest($safeBox->id(), $token, '');

        return $this->addSafeBoxItemService->execute($request);
    }

    public function test_add_item_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBox('name', $password);
        $this->safeBoxRepository->add($safeBox);

        $token = $safeBox->tokenByPassword($password, 30);

        $this->assertEmpty($safeBox->itemsByToken($token));

        $request = new AddSafeBoxItemRequest($safeBox->id(), $token, 'new_item');

        $this->addSafeBoxItemService->execute($request);

        $this->assertNotEmpty($safeBox->itemsByToken($token));
    }

}