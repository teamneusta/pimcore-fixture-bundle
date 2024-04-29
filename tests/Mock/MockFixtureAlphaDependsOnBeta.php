<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;

class MockFixtureAlphaDependsOnBeta implements DependentFixtureInterface
{
    public function load(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [
            MockFixtureBetaDependsOnGamma::class,
        ];
    }
}
