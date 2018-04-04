<?php

namespace Tests\Domain\Model\SafeBox;

use SafeBox\Domain\SafeBox\SafeBox;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use TypeError;

class SafeBoxTest extends TestCase
{

    public function test_new_safebox_not_accepts_empty_name()
    {
        $this->expectException(TypeError::class);
        new SafeBox(null, 'asdasdasdasd');
    }

    public function test_new_safebox_not_accepts_empty_password()
    {
        $this->expectException(TypeError::class);
        new SafeBox('asdasdasdasd', null);
    }

    
    public function test_new_safebox_has_valid_id()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertInstanceOf(SafeBox::class, $safeBox);

        $this->assertTrue(Uuid::isValid($safeBox->id()));

        $this->assertNotEquals($safeBox->id(), (new SafeBox($name, $password))->id());
    }

    
    public function test_encrypt_password_works()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertNotEquals($safeBox->password(), $password);
    }

    
    public function test_is_same_password_works()
    {
        $name = 'name';
        $password = 'password';

        $safeBox = new SafeBox($name, $password);

        $this->assertTrue($safeBox->isMyPassword($password));
        $this->assertFalse($safeBox->isMyPassword($password . rand(100, 999)));
    }

    
    public function test_generate_token_works()
    {
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        $token = $safeBox->tokenByPassword($password, $expiration);

        $this->assertNotNull($token);
    }

    
    public function test_invalid_token_throws_exception()
    {
        $this->expectException(\Exception::class);
        $safeBox = new SafeBox('name', 'password');
        $safeBox->validateToken('invalid_token');
    }

    
    public function test_invalid_token_from_other_safebox_throws_exception()
    {
        $this->expectException(\Exception::class);
        $password = 'password';
        $safeBox = new SafeBox('name', $password);
        $safeBox2 = new SafeBox('name', $password);

        $token = $safeBox2->tokenByPassword($password, 180);

        $this->assertFalse($safeBox->validateToken($token));
    }

    
    public function test_token_is_expired_throws_exception()
    {

        $this->expectException(\SafeBox\Domain\SafeBox\InvalidSafeBoxTokenException::class);
        $password = 'password';
        $safeBox = new SafeBox('name', $password);

        $tokenExpired = $safeBox->tokenByPassword($password, 1);

        sleep(2);

        $this->assertTrue($safeBox->validateToken($tokenExpired));
    }

    
    public function test_wrong_password_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\WrongPasswordException::class);
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        $safeBox->tokenByPassword('wrong_password', $expiration);
    }

    
    public function test_max_attempts_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\WrongPasswordException::class);
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBox('name', $password);

        for ($i = 0; $i < SafeBox::MAX_FAILED_ATTEMPTS; $i++) {
            $safeBox->tokenByPassword('wrong_password', $expiration);
        }
    }

    public function test_max_attempts_blocks_safebox_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\SafeBoxBlockedException::class);
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBoxTestClass('name', $password);
        $safeBox->setFailedAttempts(SafeBox::MAX_FAILED_ATTEMPTS -1 );
        $safeBox->tokenByPassword('wrong_password', $expiration);
    }


    public function test_blocked_safebox_throws_exception()
    {
        $this->expectException(\SafeBox\Domain\SafeBox\SafeBoxBlockedException::class);
        $password = 'password';
        $expiration = 180;

        $safeBox = new SafeBoxTestClass('name', $password);
        $safeBox->setFailedAttempts(SafeBox::MAX_FAILED_ATTEMPTS );
        $safeBox->tokenByPassword($password, $expiration);
    }

    
    public function test_add_item_encrypted_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBoxTestClass('name', $password);
        $this->assertEmpty($safeBox->getItemsByPassword($password));

        $item = 'new item';
        $safeBox->addItem($item);

        $this->assertNotEmpty($safeBox->getItems());
        $this->assertNotContains($item, $safeBox->getItems());
    }

    
    public function test_decrypt_item_works()
    {
        $password = '¡Strong_Password!';
        $safeBox = new SafeBoxTestClass('name', $password);
        $this->assertEmpty($safeBox->getItemsByPassword($password));

        $item = 'new item';
        $safeBox->addItem($item);

        $this->assertNotEmpty($safeBox->getItems());
        $this->assertContains($item, $safeBox->getItemsByPassword($password));
    }

    public function test_add_empty_item_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $safeBox = new SafeBox('name', '¡Strong_Password!');
        $safeBox->addItem('');
    }

}