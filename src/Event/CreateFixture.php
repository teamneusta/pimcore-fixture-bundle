<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Event;

final class CreateFixture
{
    /**
     * @param class-string $class
     */
    public function __construct(
        public readonly string $class,
        public readonly int $level,
    ) {
    }
}
