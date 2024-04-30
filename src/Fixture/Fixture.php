<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

interface Fixture
{
    public function create(): void;
}
