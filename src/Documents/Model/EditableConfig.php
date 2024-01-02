<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Documents\Model;

class EditableConfig
{
    /**
     * @param string|array<string> $data
     */
    public function __construct(
        public string $type,
        public string $name,
        public string|array $data = [],
    ) {
    }
}
