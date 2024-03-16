<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\CreateFixture;

final class ExecutionTreeListener
{
    /** @var list<array{fixtureClass: class-string, level: int}> */
    private array $executionTree = [];

    public function onCreateFixture(CreateFixture $event): void
    {
        $this->executionTree[] = ['fixtureClass' => $event->class, 'level' => $event->level];
    }

    /**
     * @return list<array{fixtureClass: class-string, level: int}>
     */
    public function getExecutionTree(): array
    {
        return $this->executionTree;
    }
}
