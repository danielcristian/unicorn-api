<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    const API_ROUTE = 'api/posts';

    public function testPostsListing(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::API_ROUTE);

        $this->assertResponseFormatSame('json');
        $this->assertResponseIsSuccessful();
    }

    public function testPostsListingMethodNotAllowed(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_PATCH, self::API_ROUTE);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testAddPost(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            'email' => 'testNewPost@test.com',
            "displayName" => 'Test User',
            'message' => 'Post content'
        ];

        $client->request(Request::METHOD_POST, self::API_ROUTE, $data);

        $this->assertSame($data['message'], json_decode($client->getResponse()->getContent())->message);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testAddPostInvalid(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            "displayName" => 'Test User',
            'message' => 'Post content'
        ];

        $client->request(Request::METHOD_POST, self::API_ROUTE, $data);

        $this->assertSame(
            'This value should not be blank.',
            current(json_decode($client->getResponse()->getContent())->errors->email)
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEditPost(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            'email' => 'testNewPost@test.com',
            "displayName" => 'Test User',
            'message' => 'Edit Post content'
        ];

        $client->request(Request::METHOD_POST, self::API_ROUTE, $data);

        $this->assertSame($data['message'], json_decode($client->getResponse()->getContent())->message);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteMessage(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            'email' => 'testNewPost@test.com',
            "displayName" => 'Test User',
            'message' => 'Edit Post content'
        ];

        $client->request(Request::METHOD_DELETE, self::API_ROUTE . '/2/user/2', $data);
        $this->assertSame('Post deleted succesfully.', json_decode($client->getResponse()->getContent()));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeletePostNotFound(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            'email' => 'testNewPost@test.com',
            "displayName" => 'Test User',
            'message' => 'Edit Post content'
        ];

        $client->request(Request::METHOD_DELETE, self::API_ROUTE . '/2/user/2', $data);
        $this->assertSame('Post not found.', json_decode($client->getResponse()->getContent())->errors->message);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeletePostNotAuthorized(): void
    {
        $client = static::createClient();
        $data = [
            'unicornId' => '2',
            'email' => 'testNewPost@test.com',
            "displayName" => 'Test User',
            'message' => 'Edit Post content'
        ];

        $client->request(Request::METHOD_DELETE, self::API_ROUTE . '/1/user/2', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
