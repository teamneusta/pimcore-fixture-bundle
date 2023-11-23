<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Helper;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Service;

class AssetHelper
{
    public const SHOP_PREFIX = '/shop';
    public const DEV_FOLDER = '/dev';

    public static function createAssetFolder(string $path = ''): Asset\Folder
    {
        return Service::createFolderByPath(self::SHOP_PREFIX . self::DEV_FOLDER . $path);
    }
}
