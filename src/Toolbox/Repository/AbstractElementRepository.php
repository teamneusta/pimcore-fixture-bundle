<?php

declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Toolbox\Repository;

use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Listing\AbstractListing;

/**
 * @method ElementInterface|null getById(int $id, array $params = [])
 * @method ElementInterface|null getByPath(string $path)
 * @method AbstractListing       getList(array $config = [])
 * @method int                   getTotalCount(array $config = [])
 * @method string[]              getTypes()
 */
abstract class AbstractElementRepository
{
    /**
     * @param class-string $repositoryClass
     */
    public function __construct(
        protected string $repositoryClass,
    ) {
    }

    /**
     * @param mixed[] $arguments
     *
     * @throws \Error
     */
    public function __call(string $name, array $arguments): mixed
    {
        $staticMethod = "{$this->repositoryClass}::{$name}";

        if (\is_callable($staticMethod)) {
            return $staticMethod(...$arguments);
        }

        throw new \Error(\sprintf('Call to undefined method %s::%s()', static::class, $name));
    }
}
