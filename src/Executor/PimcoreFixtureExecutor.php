<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Executor;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;

final class PimcoreFixtureExecutor implements Executor
{
    public function execute(Fixture $fixture): void
    {
        $fixture->create();
    }
}
