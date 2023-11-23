<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\Document;

class DocumentHelper
{
    public function __construct(private readonly string $prefix)
    {
    }

    public function createFolderByPath(string $path, string $locale = ''): Document\Folder
    {
        return Document\Service::createFolderByPath($this->getFullPathByPath($path, $locale));
    }

    public function getFullPathByPath(string $path, string $locale = ''): string
    {
        $locale = $locale ? '/' . $locale : '';

        return $locale . '/' . trim($this->prefix, '/') . '/' . ltrim($path, '/');
    }
}
