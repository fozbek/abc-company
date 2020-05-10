<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    /**
     * @var array
     */
    private $orderToUpdate;
    /**
     * @var array
     */
    private $orderToSee;

    protected function setUp(): void
    {
        $this->createOrderFor($this->orderToUpdate);
        $this->createOrderFor($this->orderToSee);
    }

    public function testStore()
    {
        $client = self::getClient();
        $client->request('PUT', '/api/order');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client = $this->createAuthenticatedClient();
        $client->request('PUT', '/api/order', [
            'quantity' => 15,
            'address' => 'somewhere in the world',
            'product_id' => 45674436464356, // a wrong product id
        ]);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client = $this->createAuthenticatedClient();
        $client->request('PUT', '/api/order', [
            'quantity' => 15,
            'address' => 'somewhere in the world',
            'product_id' => $_ENV['VALID_PRODUCT_ID']
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDetail()
    {
        $client = self::getClient();
        $client->request('GET', '/api/order/1');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/order/' . $this->orderToSee['id']);

        $order = json_decode($client->getResponse()->getContent(), true)['data'];
        $this->assertEquals($this->orderToSee, $order);
    }

    public function testList()
    {
        $client = self::getClient();
        $client->request('GET', '/api/order');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/order');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdate()
    {
        $client = self::getClient();
        $client->request('POST', '/api/order/' . $this->orderToUpdate['id']);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        $client = $this->createAuthenticatedClient();
        $client->request('POST', '/api/order/' . $this->orderToUpdate['id'], [
            'quantity' => 15,
            'address' => 'new adress'
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Create a client with a default Authorization header.
     *
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient()
    {
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $_ENV['VALID_USER_NAME'],
                'password' => $_ENV['VALID_USER_PASSWORD'],
            ]),
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = self::getClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    private static function getClient()
    {
        self::ensureKernelShutdown();
        return static::createClient();
    }

    private function createOrderFor(&$order)
    {
        $client = $this->createAuthenticatedClient();
        $client->request('PUT', '/api/order', [
            'quantity' => 3,
            'address' => 'valid order\'s address',
            'product_id' => $_ENV['VALID_PRODUCT_ID']
        ]);

        $order = json_decode($client->getResponse()->getContent(), true)['data'];
    }
}
