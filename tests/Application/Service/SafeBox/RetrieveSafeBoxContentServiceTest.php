<?php

namespace Tests\Application\Service\SafeBox;

use AdsMurai\Application\Service\SafeBox\RetrieveSafeBoxContentRequest;
use AdsMurai\Application\Service\SafeBox\RetrieveSafeBoxContentService;
use AdsMurai\Domain\SafeBox\SafeBox;

use AdsMurai\Infrastructure\Repository\SafeBox\MemorySafeBoxRepository;
use Tests\Domain\Model\SafeBox\SafeBoxTestClass;
use Tests\TestCase;

class RetrieveSafeBoxContentServiceTest extends TestCase
{
    /**
     * @var MemorySafeBoxRepository
     */
    private $safeBoxRepository;
    /**
     * @var RetrieveSafeBoxContentService
     */
    private $retrieveSafeBoxContentService;

    public function setUp()
    {
        parent::setUp();

        $this->safeBoxRepository = new MemorySafeBoxRepository;
        $this->retrieveSafeBoxContentService = new RetrieveSafeBoxContentService($this->safeBoxRepository);
    }

    /**
     * @test
     * @expectedException \AdsMurai\Domain\SafeBox\SafeBoxNotExistsException
     */
    public function wrong_id_throws_exception()
    {
        $request = new RetrieveSafeBoxContentRequest('not_exists_id', 'invalid_token');

        return $this->retrieveSafeBoxContentService->execute($request);
    }

    /**
     * @test
     * @expectedException \AdsMurai\Domain\SafeBox\InvalidSafeBoxTokenException
     */
    public function wrong_token_throws_exception()
    {
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $this->safeBoxRepository->add($safeBox);
        $request = new RetrieveSafeBoxContentRequest($safeBox->id(), 'invalid_token');

        return $this->retrieveSafeBoxContentService->execute($request);
    }

    /**
     * @test
     */
    public function retrieve_content_service_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBoxTestClass('name', $password);
        $safeBox->addItem('first item');
        $safeBox->addItem('second item');

        $this->safeBoxRepository->add($safeBox);

        $token = $safeBox->tokenByPassword($password, 30);

        $request = new RetrieveSafeBoxContentRequest($safeBox->id(), $token);

        $items = $this->retrieveSafeBoxContentService->execute($request);

        $this->assertNotEmpty($items);
        $this->assertEquals(count($items), count($safeBox->getItemsByPassword($password)));
    }

}