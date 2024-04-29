<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Tests\Mock;

use Neusta\Pimcore\FixtureBundle\Fixture\DependentFixtureInterface;

class MockFixtureGammaDependsOnAlpha implements DependentFixtureInterface
{
    public function load(): void
    {
        // no-op
    }

    public function getDependencies(): array
    {
        return [
            MockFixtureAlphaDependsOnBeta::class,
        ];
    }
}
