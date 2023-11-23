<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Documents;

use Neusta\Pimcore\FixtureBundle\Documents\Model\EditableConfig;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Areablock;
use Pimcore\Model\Document\PageSnippet;

trait AddEditableTrait
{
    private function addEditable(PageSnippet $pageSnippet, EditableConfig $config): void
    {
        /** @var Editable $editable */
        $editable = new $config->type();
        $editable->setName($config->name);
        $editable->setDataFromEditmode($config->data);
        $pageSnippet->setEditable($editable);
    }

    private function addAreabrick(PageSnippet $pageSnippet, string $areablockName, string $type, string $key): void
    {
        $areablock = $pageSnippet->getEditable($areablockName);
        if (null === $areablock) {
            return;
        }
        $data = $areablock->getData();
        $data[] = [
            'key' => $key,
            'type' => $type,
            'hidden' => false,
        ];
        $areablock->setDataFromEditmode($data);
        $pageSnippet->setEditable($areablock);
    }

    private function addAreaBlock(PageSnippet $pageSnippet, string $areablockName): void
    {
        $this->addEditable(
            $pageSnippet,
            new EditableConfig(
                Areablock::class,
                $areablockName,
            ),
        );
    }

    private function updateBlockAfterAddingBlockItem(PageSnippet $pageSnippet, string $blockName): void
    {
        $block = $pageSnippet->getEditable($blockName);
        if (!$block) {
            return;
        }
        $block->setDataFromResource($block->getDataForResource());
        $pageSnippet->setEditable($block);
    }
}
