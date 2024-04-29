<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

class UnresolvedFixtureDependency extends \OutOfRangeException
{
    public function __construct(string $name = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'UnresolvedFixtureDependency: Fixture "%s" not found',
            $name,
        ), $code, $previous);
    }
}
