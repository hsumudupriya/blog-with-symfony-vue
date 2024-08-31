<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthenticationTest extends ApiTestCase
{
    private function makeApiLoginRequest(array $json): ResponseInterface
    {
        // create the HTTP client
        $client = self::createClient();

        // make the api/login request
        return $client->request('POST', 'api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $json,
        ]);
    }

    public function testEmptyLoginRequestReturnsClientError(): void
    {
        // try retrieving a token with empty credentials
        $response = $this->makeApiLoginRequest([
            'email' => '',
            'password' => '',
        ]);
        $array = $response->toArray(throw: false);

        // assert the status code of the response is 400 and does not contain a token
        $this->assertResponseStatusCodeSame(400);
        $this->assertArrayNotHasKey('token', $array);
    }

    public function testLoginRequestWithInvalidCredentialsReturnsClientError(): void
    {
        // create a test user
        UserFactory::findOrCreate([
            "email" => 'test@api.com',
            "password" => 'abc123',
        ]);

        // try retrieving a token with invalid credentials
        $response = $this->makeApiLoginRequest([
            'email' => 'test123@api.com',
            'password' => 'abc456',
        ]);
        $array = $response->toArray(throw: false);

        // assert the status code of the response is 401 and does not contain a token
        $this->assertResponseStatusCodeSame(401);
        $this->assertArrayNotHasKey('token', $array);
    }

    public function testLoginRequestReturnsToken(): void
    {
        // create a test user
        $email = 'test@api.com';
        $password = 'abc123';

        UserFactory::findOrCreate([
            "email" => $email,
            "password" => $password,
        ]);

        // try retrieving a token with invalid credentials
        $response = $this->makeApiLoginRequest([
            'email' => $email,
            'password' => $password,
        ]);
        $array = $response->toArray();

        // assert the status code of the response is 200 and has a token
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $array);
    }
}
