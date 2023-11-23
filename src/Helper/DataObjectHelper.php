<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\DataObject;

class DataObjectHelper
{
    public const SHOP_PREFIX = '/shop';
    public const DEV_FOLDER = '/dev';

    public static function createFolderByPath(string $path = ''): DataObject\Folder
    {
        return DataObject\Service::createFolderByPath(self::SHOP_PREFIX . self::DEV_FOLDER . $path);
    }

    public static function getFullPathByPath(string $path): string
    {
        return self::SHOP_PREFIX . self::DEV_FOLDER . $path;
    }
}
