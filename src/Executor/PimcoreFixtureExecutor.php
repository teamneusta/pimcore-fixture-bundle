<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Executor;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

class PimcoreFixtureExecutor implements ExecutorInterface
{
    public function execute(FixtureInterface $fixture): void
    {
        $fixture->load();
    }
}
