<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Fixtures;

use Neusta\Pimcore\FixtureBundle\Fixture;

final class FixtureWithDependency implements Fixture
{
    public float $createdAt;

    public function create(DependantFixture $fixture): void
    {
        $this->createdAt = microtime(true);
    }
}
