<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

class FixtureNotFound extends \OutOfBoundsException
{
    /**
     * @param list<class-string<Fixture>> $fixturesToLoad
     */
    public static function forFixtures(array $fixturesToLoad): self
    {
        return new self(\sprintf(
            'Fixtures not found: "%s". Maybe you forgot to register your Fixture as a Service?',
            implode('", "', $fixturesToLoad),
        ));
    }
}
