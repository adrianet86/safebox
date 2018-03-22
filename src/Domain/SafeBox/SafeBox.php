<?php

namespace SafeBox\Domain\SafeBox;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class SafeBox
{
    const MAX_FAILED_ATTEMPTS = 3;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;
    private $name;
    private $password;
    private $items;

    protected $failedAttempts;

    private $encryptMethod = "AES-256-CBC";
    private $secretKey = 'SafeBox to SafeBox secret key';
    private $secretIv = 'SafeBox to SafeBox secret iv';

    /**
     * SafeBox constructor.
     * @param string $name
     * @param string $password
     */
    public function __construct(string $name, string $password)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->password = $this->encrypt($password);
        $this->failedAttempts = 0;
        $this->items = [];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): string
    {
        return $this->password;
    }

    private function encrypt(string $content, string $key = '')
    {
        $output = openssl_encrypt($content, $this->encryptMethod, $this->key($key), 0, $this->iv());

        return base64_encode($output);
    }

    private function decrypt(string $content, string $key = ''): string
    {
        return openssl_decrypt(base64_decode($content), $this->encryptMethod, $this->key($key), 0, $this->iv());
    }

    private function key(string $key = ''): string
    {
        if (empty($key)) {
            $key = $this->secretKey;
        }
        return hash('sha256', $key);
    }

    private function iv(): string
    {
        return substr(hash('sha256', $this->secretIv), 0, 16);
    }

    public function isMyPassword(string $password): bool
    {
        return $this->password() === $this->encrypt($password);
    }

    /**
     * @param string $password
     * @param int $expiration
     * @return string
     * @throws SafeBoxBlockedException
     * @throws WrongPasswordException
     */
    public function tokenByPassword(string $password, int $expiration): string
    {
        if ($this->isMyPassword($password) && $this->failedAttempts < self::MAX_FAILED_ATTEMPTS) {
            $expirationDate = Carbon::now()->addSeconds($expiration)->toDateTimeString();

            return $this->encrypt($expirationDate, $this->id());
        }

        $this->failedAttempts++;

        if ($this->failedAttempts >= self::MAX_FAILED_ATTEMPTS) {
            throw new SafeBoxBlockedException('Max failed attempts, safebox is blocked', 423);
        }

        throw new WrongPasswordException('Wrong safebox password', 401);
    }

    /**
     * @param string $token
     * @throws InvalidSafeBoxTokenException
     */
    public function validateToken(string $token): void
    {
        $expirationDate = $this->decrypt($token, $this->id());

        if (empty($expirationDate)) {
            throw new InvalidSafeBoxTokenException('Invalid token', 401);
        }

        $now = Carbon::now()->toDateTimeString();

        if ($expirationDate < $now === true) {
            throw new InvalidSafeBoxTokenException('Token expired', 401);
        }

    }

    public function addItem(string $string)
    {
        if (empty($string)) {
            throw new \InvalidArgumentException('Empty item!');
        }
        $this->items[] = $this->encrypt($string, $this->id());
    }

    /**
     * @param string $token
     * @return array
     * @throws InvalidSafeBoxTokenException
     */
    public function itemsByToken(string $token): array
    {
        $this->validateToken($token);
        $itemsDecrypted = [];

        foreach ($this->items() as $item) {
            $itemsDecrypted[] = $this->decrypt($item, $this->id());
        }

        return $itemsDecrypted;
    }

    protected function items(): array
    {
        return $this->items;
    }

}