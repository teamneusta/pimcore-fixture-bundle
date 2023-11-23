<?php
declare(strict_types=1);

namespace NspPimcore\FixtureBase;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FixtureBaseBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
