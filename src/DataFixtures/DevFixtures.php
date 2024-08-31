<?php

namespace App\DataFixtures;

use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DevFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create a test user for the API documentation
        UserFactory::findOrCreate([
            'email' => 'test@api.com',
            'password' => 'abc123',
        ]);
        // create test blog posts.
        BlogPostFactory::createMany(number: 40);
    }
}
