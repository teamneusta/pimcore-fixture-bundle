<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Service;

class AssetHelper
{
    public function __construct(private readonly string $prefix)
    {
    }

    public function createAssetFolder(string $path = ''): Asset\Folder
    {
        return Service::createFolderByPath('/' . trim($this->prefix, '/') . '/' . ltrim($path, '/'));
    }
}
