<?php

namespace Tests\Application\Service\SafeBox;

use SafeBox\Application\Service\SafeBox\CreateNewSafeBoxRequest;
use SafeBox\Application\Service\SafeBox\CreateNewSafeBoxService;
use SafeBox\Domain\SafeBox\SafeBox;
use SafeBox\Infrastructure\Repository\SafeBox\FileCommonPasswordRepository;
use SafeBox\Infrastructure\Repository\SafeBox\MemorySafeBoxRepository;
use Tests\TestCase;

class CreateNewSafeBoxServiceTest extends TestCase
{
    /**
     * @var FileCommonPasswordRepository
     */
    private $commonPasswordRepository;
    /**
     * @var MemorySafeBoxRepository
     */
    private $safeBoxRepository;
    /**
     * @var CreateNewSafeBoxService
     */
    private $createNewSafeBoxService;

    public function setUp()
    {
        parent::setUp();

        $this->commonPasswordRepository = new FileCommonPasswordRepository();
        $this->safeBoxRepository = new MemorySafeBoxRepository;
        $this->createNewSafeBoxService = new CreateNewSafeBoxService(
            $this->safeBoxRepository,
            $this->commonPasswordRepository
        );
    }

    private function executeCreateNewSafeBox()
    {
        return $this->createNewSafeBoxService->execute(new CreateNewSafeBoxRequest('name', '¡Strong_Password!'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function bad_password_throws_exception()
    {
        $this->createNewSafeBoxService->execute(new CreateNewSafeBoxRequest('name', ''));
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\SafeBoxExistsException
     */
    public function box_already_exists_throws_exception_mock_version()
    {
        $name = 'name';
        $password = '¡Strong_Password!';

        $safeBoxRepositoryMock = $this->createMock(MemorySafeBoxRepository::class);
        $safeBoxRepositoryMock
            ->method('byName')
            ->willReturn(new SafeBox($name, $password));

        $this->createNewSafeBoxService = new CreateNewSafeBoxService(
            $safeBoxRepositoryMock,
            $this->commonPasswordRepository
        );

        $this->createNewSafeBoxService->execute(new CreateNewSafeBoxRequest($name, $password));
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\SafeBoxExistsException
     */
    public function box_already_exists_throws_exception()
    {
        $this->executeCreateNewSafeBox();
        $this->executeCreateNewSafeBox();
    }

    /**
     * @test
     */
    public function after_create_new_sandbox_is_in_the_repository()
    {
        $safeBox = $this->executeCreateNewSafeBox();

        $this->assertNotNull($this->safeBoxRepository->byId($safeBox->id()));
    }
}