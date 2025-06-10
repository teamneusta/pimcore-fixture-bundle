<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

final class NeustaPimcoreFixtureBundle extends AbstractPimcoreBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
