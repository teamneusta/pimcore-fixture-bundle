<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

use Neusta\Pimcore\FixtureBundle\Fixture\FixtureInterface;

final class BeforeExecuteFixture
{
    private bool $preventExecution = false;

    public function __construct(
        public readonly FixtureInterface $fixture,
    ) {
    }

    public function preventExecution(): bool
    {
        return $this->preventExecution;
    }

    public function setPreventExecution(bool $preventExecution): void
    {
        $this->preventExecution = $preventExecution;
    }
}
