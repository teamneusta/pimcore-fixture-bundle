<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Sorter;

class CircularFixtureDependencyException extends \RuntimeException
{
    public function __construct(string $name = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'CircularFixtureDependency: Circular Reference detected in Fixture "%s"',
            $name,
        ), $code, $previous);
    }
}
