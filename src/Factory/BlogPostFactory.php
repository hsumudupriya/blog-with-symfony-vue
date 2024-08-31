<?php

namespace App\Factory;

use App\Entity\BlogPost;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BlogPost>
 */
final class BlogPostFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return BlogPost::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $title = self::faker()->sentence();

        return [
            'content' => self::faker()->sentence(nbWords: 100),
            'isPublished' => self::faker()->boolean(),
            'picture' => self::faker()->imageUrl(word: $title),
            'title' => $title,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(BlogPost $blogPost): void {})
        ;
    }
}
