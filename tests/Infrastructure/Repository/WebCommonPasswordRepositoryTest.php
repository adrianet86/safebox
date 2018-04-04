<?php

namespace Tests\Infrastructure\Repository;

use SafeBox\Infrastructure\Repository\SafeBox\WebCommonPasswordRepository;
use Tests\TestCase;

class WebCommonPasswordRepositoryTest extends TestCase
{

    public function test_web_common_password_repository_works()
    {
        $webCommonPasswordRepository = new WebCommonPasswordRepository();
        $passwords = $webCommonPasswordRepository->all();

        $this->assertNotEmpty($passwords);
    }
}