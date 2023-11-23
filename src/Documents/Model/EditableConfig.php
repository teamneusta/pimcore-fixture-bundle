<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Documents\Model;

class EditableConfig
{
    public function __construct(
        public string $type,
        public string $name,
        public string|array $data = [],
    ) {
    }
}
