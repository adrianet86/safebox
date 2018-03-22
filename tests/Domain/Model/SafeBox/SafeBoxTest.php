<?php

namespace Tests\Domain\Model\SafeBox;

use SafeBox\Domain\SafeBox\SafeBox;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use TypeError;

class SafeBoxTest extends TestCase
{

    /**
     * @test
     * @expectedException TypeError
     */
    public function new_safebox_not_accepts_empty_name()
    {
        new SafeBox(null, 'asdasdasdasd');
    }

    /**
     * @test
     * @expectedException TypeError
     */
    public function new_safebox_not_accepts_empty_password()
    {
        new SafeBox('asdasdasdasd', null);
    }

    /**
     * @test
     */
    public function new_safebox_has_valid_id()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertInstanceOf(SafeBox::class, $safeBox);

        $this->assertTrue(Uuid::isValid($safeBox->id()));

        $this->assertNotEquals($safeBox->id(), (new SafeBox($name, $password))->id());
    }

    /**
     * @test
     */
    public function encrypt_password_works()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertNotEquals($safeBox->password(), $password);
    }

    /**
     * @test
     */
    public function is_same_password_works()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertTrue($safeBox->isMyPassword($password));
        $this->assertFalse($safeBox->isMyPassword($password . rand(100, 999)));
    }

    /**
     * @test
     */
    public function generate_token_works()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        $token = $safeBox->tokenByPassword($password, $expiration);

        $this->assertNotNull($token);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function invalid_token_throws_exception()
    {
        $safeBox = new SafeBox('name', 'password');

        $this->assertFalse($safeBox->validateToken('invalid_token'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function invalid_token_from_other_safebox_throws_exception()
    {
        $password = 'password';
        $safeBox = new SafeBox('name', $password);
        $safeBox2 = new SafeBox('name', $password);

        $token = $safeBox2->tokenByPassword($password, 180);

        $this->assertFalse($safeBox->validateToken($token));
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\InvalidSafeBoxTokenException
     */
    public function token_is_expired_throws_exception()
    {
        $password = 'password';
        $safeBox = new SafeBox('name', $password);

        $tokenExpired = $safeBox->tokenByPassword($password, 1);

        sleep(2);

        $this->assertTrue($safeBox->validateToken($tokenExpired));
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\WrongPasswordException
     */
    public function wrong_password_throws_exception()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        $safeBox->tokenByPassword('wrong_password', $expiration);
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\WrongPasswordException
     */
    public function max_attempts_throws_exception()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        for ($i = 0; $i < SafeBox::MAX_FAILED_ATTEMPTS; $i++) {
            $safeBox->tokenByPassword('wrong_password', $expiration);
        }
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\SafeBoxBlockedException
     */
    public function max_attempts_blocks_safebox_exception()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBoxTestClass('name', $password);
        $safeBox->setFailedAttempts(SafeBox::MAX_FAILED_ATTEMPTS -1 );
        $safeBox->tokenByPassword('wrong_password', $expiration);
    }

    /**
     * @test
     * @expectedException \SafeBox\Domain\SafeBox\SafeBoxBlockedException
     */
    public function blocked_safebox_throws_exception()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBoxTestClass('name', $password);
        $safeBox->setFailedAttempts(SafeBox::MAX_FAILED_ATTEMPTS );
        $safeBox->tokenByPassword($password, $expiration);
    }

    /**
     * @test
     */
    public function add_item_encrypted_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBoxTestClass('name', $password);
        $this->assertEmpty($safeBox->getItemsByPassword($password));

        $item = 'new item';
        $safeBox->addItem($item);

        $this->assertNotEmpty($safeBox->getItems());
        $this->assertNotContains($item, $safeBox->getItems());
    }

    /**
     * @test
     */
    public function decrypt_item_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBoxTestClass('name', $password);
        $this->assertEmpty($safeBox->getItemsByPassword($password));

        $item = 'new item';
        $safeBox->addItem($item);

        $this->assertNotEmpty($safeBox->getItems());
        $this->assertContains($item, $safeBox->getItemsByPassword($password));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function add_empty_item_throws_exception()
    {
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $safeBox->addItem('');
    }

}