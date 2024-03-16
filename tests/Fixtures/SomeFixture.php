<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Fixtures;

use Neusta\Pimcore\FixtureBundle\Fixture;

final class SomeFixture implements Fixture
{
    public bool $created;

    public function create(): void
    {
        $this->created = true;
    }
}
