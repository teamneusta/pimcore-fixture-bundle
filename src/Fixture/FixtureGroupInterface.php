<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

/**
 * FixtureGroupInterface can to be implemented by fixtures that belong in groups
 * inspired by \Doctrine\Bundle\FixturesBundle\FixtureGroupInterface.
 */
interface FixtureGroupInterface extends FixtureInterface
{
    /**
     * This method must return an array of groups
     * on which the implementing class belongs to.
     *
     * @return array<string>
     */
    public static function getGroups(): array;
}
