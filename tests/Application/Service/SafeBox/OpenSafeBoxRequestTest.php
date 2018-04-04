<?php
/**
 * Created by PhpStorm.
 * User: adria
 * Date: 18/03/18
 * Time: 10:42
 */

namespace Tests\Application\Service\SafeBox;


use SafeBox\Application\Service\SafeBox\OpenSafeBoxRequest;
use Tests\TestCase;

class OpenSafeBoxRequestTest extends TestCase
{

    public function test_wrong_expiration_throws_exception()
    {
        $this->expectException(\Exception::class);
        new OpenSafeBoxRequest('id', 'password', 0);
    }

}