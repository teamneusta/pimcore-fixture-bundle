<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

/**
 * The FixtureGroup interface can to be implemented by fixtures that belong in groups.
 *
 * It is inspired by: \Doctrine\Bundle\FixturesBundle\FixtureGroupInterface
 */
interface FixtureGroup
{
    /**
     * This method must return a list of groups on which the implementing class belongs to.
     *
     * @return list<string>
     */
    public static function getGroups(): array;
}
