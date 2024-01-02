<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NeustaPimcoreFixtureBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
