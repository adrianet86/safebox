<?php

namespace Tests\Application\Service\SafeBox;

use SafeBox\Application\Service\SafeBox\OpenSafeBoxRequest;
use SafeBox\Application\Service\SafeBox\OpenSafeBoxService;
use SafeBox\Domain\SafeBox\SafeBox;

use SafeBox\Infrastructure\Repository\SafeBox\MemorySafeBoxRepository;
use Tests\TestCase;

class OpenSafeBoxServiceTest extends TestCase
{
    /**
     * @var MemorySafeBoxRepository
     */
    private $safeBoxRepository;
    /**
     * @var OpenSafeBoxService
     */
    private $openSafeBoxService;

    public function setUp()
    {
        parent::setUp();

        $this->safeBoxRepository = new MemorySafeBoxRepository;
        $this->openSafeBoxService = new OpenSafeBoxService($this->safeBoxRepository);
    }

    private function executeOpenSafeBoxService()
    {
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $this->safeBoxRepository->add($safeBox);

        return $this->openSafeBoxService->execute(new OpenSafeBoxRequest($safeBox->id(), '¡Strong_Password!', 10));

    }

    public function test_not_found_safebox_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\SafeBoxNotExistsException::class);
        $this->openSafeBoxService->execute(new OpenSafeBoxRequest('id_no_existente', ''));
    }

    public function test_wrong_password_safebox_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\WrongPasswordException::class);
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $this->safeBoxRepository->add($safeBox);
        $this->openSafeBoxService->execute(new OpenSafeBoxRequest($safeBox->id(), 'wrong_password'));
    }

    public function test_wrong_expiration_time_throws_exception()
    {
        $this->expectException(\Exception::class);
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $this->safeBoxRepository->add($safeBox);
        $this->openSafeBoxService->execute(new OpenSafeBoxRequest($safeBox->id(), '¡Strong_Password!', 0));
    }

    /**
     * @test
     */
    public function open_safebox_service_generates_token()
    {
        $token = $this->executeOpenSafeBoxService();

        $this->assertNotNull($token);
    }


}