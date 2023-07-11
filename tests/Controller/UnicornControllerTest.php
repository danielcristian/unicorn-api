<?php

namespace App\Tests\Controlller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnicornControllerTest extends WebTestCase
{
    public function testUnicornsListing(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, 'api/unicorns');

        $this->assertResponseFormatSame('json');
        $this->assertResponseIsSuccessful();
    }

    public function testUnicornsListingMethodNotAllowed(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, 'api/unicorns');
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testUnicornsPurchase(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, 'api/unicorns/1/purchase', [
            'email' => 'testPurchase@test.com'
        ]);

        $this->assertEmailCount(1);
        $this->assertResponseIsSuccessful();
    }

    public function testUnicornsAlreadyPurchased(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, 'api/unicorns/1/purchase', [
            'email' => 'testPurchase@test.com'
        ]);
        $this->assertEmailCount(0);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUnicornsNotFound(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, 'api/unicorns/12323222/purchase', [
            'email' => 'testPurchase@test.com'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
