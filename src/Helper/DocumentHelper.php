<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Helper;

use Pimcore\Model\Document;

class DocumentHelper
{
    public const SHOP_PREFIX = '/shop';

    public static function createFolderByPath(string $path, string $locale = ''): Document\Folder
    {
        $locale = $locale ? '/' . $locale : '';

        return Document\Service::createFolderByPath($locale . self::SHOP_PREFIX . $path);
    }

    public static function getFullPathByPath(string $path, string $locale = ''): string
    {
        $locale = $locale ? '/' . $locale : '';

        return $locale . self::SHOP_PREFIX . $path;
    }
}
