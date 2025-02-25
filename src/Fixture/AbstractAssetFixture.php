<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Fixture;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;

abstract class AbstractAssetFixture extends AbstractFixture
{
    public function __construct(
        protected string $fullQualifiedFilename,
        protected string $pimcoreFilename,
        protected string $pimcoreBasePath,
        protected string $fixtureAssetMarker,
    ) {
    }

    public function create(): void
    {
        $asset = new Image();
        $asset->setFilename($this->pimcoreFilename);
        $asset->setData(file_get_contents($this->fullQualifiedFilename));
        $asset->setParent(Asset::getByPath($this->pimcoreBasePath));

        $this->replaceIfExists($asset);

        $this->addReference($this->fixtureAssetMarker, $asset);
    }

    private function replaceIfExists(Image $asset): void
    {
        $oldAsset = Asset::getByPath($this->pimcoreBasePath . $asset->getFullPath());
        if (null !== $oldAsset) {
            $oldAsset->delete();
        }

        $asset->save();
    }
}
