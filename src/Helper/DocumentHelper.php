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
        $documentFolder = Document\Service::createFolderByPath($this->getFullPathByPath($path, $locale));
        if ($documentFolder instanceof Document\Folder) {
            return $documentFolder;
        }
        throw new \Exception(\sprintf('No document folder with path %s could be created.', $path));
    }

    public function getFullPathByPath(string $path, string $locale = ''): string
    {
        $locale = $locale ? '/' . $locale : '';

        return $locale . '/' . trim($this->prefix, '/') . '/' . ltrim($path, '/');
    }
}
