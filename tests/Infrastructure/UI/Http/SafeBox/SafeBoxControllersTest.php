<?php

namespace Tests\Infrastructure\UI\Http\SafeBox;

use SafeBox\Domain\SafeBox\SafeBox;
use SafeBox\Infrastructure\Repository\SafeBox\SqliteSafeBoxRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;


class SafeBoxControllersTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $faker;
    private $headers;
    /**
     * @var SqliteSafeBoxRepository
     */
    private $sqliteSafeBoxRepository;
    /**
     * @var SafeBox
     */
    private $existingSafeBox;
    private $password;
    private $url;

    public function setUp()
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
        $this->sqliteSafeBoxRepository = new SqliteSafeBoxRepository();
        $this->password = $this->faker->password;
        $this->existingSafeBox = new SafeBox($this->faker->name, $this->password);
        $this->sqliteSafeBoxRepository->add($this->existingSafeBox);
        $this->headers = ['Accept' => 'application/json'];//'Content-type' => 'application/json',
        $this->url = '/v1/safebox/';
    }

    /**
     * @test
     */
    public function create_new_safe_box_works()
    {
        $response = $this->post($this->url, ['name' => $this->faker->name, 'password' => '¡Safe_password!']);

        $response->assertStatus(200);
        $this->assertTrue(Uuid::isValid($response->json('id')));
    }

    /**
     * @test
     */
    public function existing_safe_box_error_409()
    {
        $data = ['name' => $this->existingSafeBox->name(), 'password' => $this->password];

        $responseError = $this->post($this->url, $data, $this->headers);
        $responseError->assertStatus(409);
    }

    /**
     * @test
     */
    public function create_safebox_with_wrong_data_error_422_malformed_data()
    {
        $data = ['name' => ''];

        $responseError = $this->post($this->url, $data, $this->headers);
        $responseError->assertStatus(422);
    }

    /**
     * @test
     */
    public function open_safe_box_works()
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->password;

        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);

        $response->assertStatus(200);
        $token = $response->json('token');

        $this->assertNotNull($token);
    }

    /**
     * @test
     */
    public function expired_token_error_401()
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->password;

        $response = $this->json(
            'GET',
            $this->url . $this->existingSafeBox->id() . "/open",
            ['expirationTime' => 1],
            $this->headers
        );

        $response->assertStatus(200);
        $token = $response->json('token');

        $this->assertNotNull($token);
        sleep(3);

        $this->headers['Authorization'] = 'Bearer ' . $token;
        $response = $this->post(
            $this->url . $this->existingSafeBox->id(),
            ['item' => 'my item'],
            $this->headers
        );

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function open_safe_box_not_found_error_404()
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->password;

        $response = $this->get("/safebox/not_existing_id/open", $this->headers);

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function open_safe_box_no_authorization_header_error_401()
    {
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function open_safe_box_wrong_password_error_401()
    {
        $this->headers['Authorization'] = 'Bearer wrong_password';

        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function open_with_wrong_password_many_times_locks_safe_box_error_423()
    {
        $this->headers['Authorization'] = 'Bearer wrong_password';
        //first attempt
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $response->assertStatus(401);
        //second attempt
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $response->assertStatus(401);
        //third attempt
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $response->assertStatus(423);
        //fourth attempt
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $response->assertStatus(423);

        //good password still locked
        $this->headers['Authorization'] = 'Bearer ' . $this->password;
        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $response->assertStatus(423);
    }

    /**
     * @test
     */
    public function add_safe_box_item_works()
    {
        $token = $this->existingSafeBox->tokenByPassword($this->password, 20);
        $this->headers['Authorization'] = 'Bearer ' . $token;
        $response = $this->post(
            $this->url . $this->existingSafeBox->id(),
            ['item' => 'my item'],
            $this->headers
            );

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function add_safe_box_wrong_token_error_401()
    {
        $token = $this->existingSafeBox->tokenByPassword($this->password, 20);
        $this->headers['Authorization'] = 'Bearer ' . $token;
        $response = $this->post(
            $this->url . $this->existingSafeBox->id(),
            ['item' => 'my item'],
            $this->headers
        );

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function retrieve_safe_box_content_works()
    {
        $this->headers['Authorization'] = 'Bearer ' . $this->password;
        $items = ['new item', 'second_item'];
        $this->existingSafeBox->addItem($items[0]);
        $this->sqliteSafeBoxRepository->store($this->existingSafeBox);

        $response = $this->get($this->url . $this->existingSafeBox->id() . "/open", $this->headers);
        $token = $response->json('token');

        $this->headers['Authorization'] = 'Bearer ' . $token;
        $response = $this->get($this->url . $this->existingSafeBox->id(), $this->headers);

        $response->assertStatus(200);
        $itemsResponse = $response->json('items');

        $this->assertNotNull($itemsResponse);
        $this->assertContains($items[0], $itemsResponse);

        $this->existingSafeBox->addItem($items[1]);
        $this->sqliteSafeBoxRepository->store($this->existingSafeBox);

        $response = $this->get($this->url. $this->existingSafeBox->id(), $this->headers);
        $response->assertStatus(200);
        $itemsResponse2 = $response->json('items');

        $this->assertEquals(count($items), count($itemsResponse2));
    }


}
