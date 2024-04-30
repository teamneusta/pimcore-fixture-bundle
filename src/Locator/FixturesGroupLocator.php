<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Locator;

use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasGroups;

final class FixturesGroupLocator implements FixtureLocator
{
    /** @var list<string> */
    private array $groupNamesToLoad = [];

    /**
     * @param \Traversable<Fixture> $allFixtures
     */
    public function __construct(
        private readonly \Traversable $allFixtures,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getGroupsToLoad(): array
    {
        return $this->groupNamesToLoad;
    }

    /**
     * @param list<string> $groupNames
     *
     * @return $this
     */
    public function setGroupsToLoad(array $groupNames): self
    {
        $this->groupNamesToLoad = $groupNames;

        return $this;
    }

    public function getFixtures(): array
    {
        if (empty($this->getGroupsToLoad())) {
            return iterator_to_array($this->allFixtures, false);
        }

        $fixtures = [];
        foreach ($this->allFixtures as $fixture) {
            if (!$fixture instanceof HasGroups) {
                continue;
            }

            if (!$this->hasAtLeastOneGroupMatch($fixture::getGroups(), $this->getGroupsToLoad())) {
                continue;
            }

            $fixtures[] = $fixture;
        }

        return $fixtures;
    }

    /**
     * @param list<string> $groupsOfTheFixture
     * @param list<string> $requestedGroups
     */
    private function hasAtLeastOneGroupMatch(array $groupsOfTheFixture, array $requestedGroups): bool
    {
        foreach ($groupsOfTheFixture as $groupName) {
            if (\in_array($groupName, $requestedGroups, true)) {
                return true;
            }
        }

        return false;
    }
}
