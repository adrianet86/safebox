<?php
/**
 * Created by PhpStorm.
 * User: adria
 * Date: 18/03/18
 * Time: 10:42
 */

namespace Tests\Application\Service\SafeBox;


use AdsMurai\Application\Service\SafeBox\OpenSafeBoxRequest;
use Tests\TestCase;

class OpenSafeBoxRequestTest extends TestCase
{
    /**
     * @test
     * @expectedException \Exception
     */
    public function wrong_expiration_throws_exception()
    {
        new OpenSafeBoxRequest('id', 'password', 0);
    }

    /**
     * @test
     */
    public function new_request_works()
    {
        $request = new OpenSafeBoxRequest('id', 'password', 1);
        $this->assertInstanceOf(OpenSafeBoxRequest::class, $request);
    }
}