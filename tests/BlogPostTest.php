<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\BlogPost;
use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;

use function Zenstruck\Foundry\faker;

class BlogPostTest extends ApiTestCase
{
    private function createAuthenticatedClient(): Client
    {
        // create a test user
        $user = UserFactory::findOrCreate([
            'email' => 'test@api.com',
            'password' => 'abc123',
        ]);

        // create an authenticated client with the above user
        $client = self::createClient();
        $client->loginUser(user: $user, firewallContext: 'api');

        return $client;
    }

    public function testUnauthorizedGetBlogPostsRequestReturnsClientError(): void
    {
        // create the HTTP client
        $client = self::createClient();

        // make the request
        $response = $client->request('GET', 'api/blog_posts', [
            'headers' => ['Content-Type' => 'application/ld+json'],
        ]);
        $array = $response->toArray(throw: false);

        // assert the status code of the response is 401 and contains an error message
        $this->assertResponseStatusCodeSame(401);
        $this->assertArrayHasKey('message', $array);
    }

    public function testAuthorizedGetBlogPostsRequest(): void
    {
        // create test blog posts
        BlogPostFactory::createMany(40);

        // make the request
        $requestQuery = [
            'itemsPerPage' => 10,
            'pagination' => true,
            'page' => 1,
        ];
        $client = $this->createAuthenticatedClient();
        $response = $client->request('GET', 'api/blog_posts', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'query' => $requestQuery
        ]);
        $array = $response->toArray();

        $this->assertResponseIsSuccessful();
        // assert that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // assert that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/BlogPost',
            '@id' => '/api/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 40,
            'hydra:view' => [
                '@id' => '/api/blog_posts?' . \http_build_query($requestQuery),
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/blog_posts?' . \http_build_query($requestQuery),
                'hydra:last' => '/api/blog_posts?' . \http_build_query(\array_replace($requestQuery, ['page' => 4])),
                'hydra:next' => '/api/blog_posts?' . \http_build_query(\array_replace($requestQuery, ['page' => 2])),
            ],
        ]);

        // assert the count of items
        $this->assertCount(10, $array['hydra:member']);
        // asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        $this->assertMatchesResourceCollectionJsonSchema(BlogPost::class);
    }

    public function testAuthorizedGetBlogPostsRequestWithSearch(): void
    {
        // create a test blog post
        BlogPostFactory::createOne();
        // create another blog post with specific content.
        $searchString = ' specific search string ';
        $content = faker()->sentence(nbWords: 50) . $searchString . faker()->sentence(nbWords: 50);
        BlogPostFactory::createOne(['content' => $content]);

        // make the request
        $requestQuery = [
            'content' => trim($searchString),
            'itemsPerPage' => 10,
            'pagination' => true,
            'page' => 1,
        ];
        $client = $this->createAuthenticatedClient();
        $response = $client->request('GET', 'api/blog_posts', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'query' => $requestQuery
        ]);
        $array = $response->toArray();

        $this->assertResponseIsSuccessful();
        // assert that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // assert that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/BlogPost',
            '@id' => '/api/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);

        // assert the count of items
        $this->assertCount(1, $array['hydra:member']);
        // assert the returned blog post contains the search string
        $this->assertStringContainsString(trim($searchString), $array['hydra:member'][0]['content']);
        // asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        $this->assertMatchesResourceCollectionJsonSchema(BlogPost::class);
    }
}
