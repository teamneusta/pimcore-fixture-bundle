<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Documents\Model;

class NavItem
{
    /**
     * @param NavItem[] $children
     */
    public function __construct(
        public string $en = '',
        public string $de = '',
        public array $children = [],
    ) {
    }
}
