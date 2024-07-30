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
        $dataObjectFolder = DataObject\Service::createFolderByPath($this->getFullPathByPath($path));
        if ($dataObjectFolder instanceof DataObject\Folder) {
            return $dataObjectFolder;
        }
        throw new \Exception(\sprintf('No data object folder with path %s could be created.', $path));
    }

    public function getFullPathByPath(string $path): string
    {
        return '/' . trim($this->prefix, '/') . '/' . ltrim($path, '/');
    }
}
