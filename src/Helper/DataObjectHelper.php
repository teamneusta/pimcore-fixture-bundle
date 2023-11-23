<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\DataObject;

class DataObjectHelper
{
    public function __construct(private readonly string $prefix)
    {
    }

    public function createFolderByPath(string $path = ''): DataObject\Folder
    {
        return DataObject\Service::createFolderByPath($this->getFullPathByPath($path));
    }

    public function getFullPathByPath(string $path): string
    {
        return '/' . trim($this->prefix, '/') . '/' . ltrim($path, '/');
    }
}
