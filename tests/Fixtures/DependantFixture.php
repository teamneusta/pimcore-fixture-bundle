<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Fixtures;

use Neusta\Pimcore\FixtureBundle\Fixture;

final class DependantFixture implements Fixture
{
    public float $createdAt;

    public function create(): void
    {
        if (isset($this->createdAt)) {
            // Ensures that this method doesn't get called twice on the same object.
            throw new \LogicException('Should never happen.');
        }

        $this->createdAt = microtime(true);

        // ensure the next fixture is not created in the same microsecond
        usleep(1);
    }
}
