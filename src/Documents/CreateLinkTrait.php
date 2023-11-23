<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Documents;

use Pimcore\Model\Document;
use Pimcore\Model\Document\Link;
use Pimcore\Model\Document\Service;
use Pimcore\Model\Element\AbstractElement;

trait CreateLinkTrait
{
    private function createInternalLink(
        string $key,
        Document $parent,
        string $navigationTitle,
        AbstractElement $element,
    ): Link {
        $link = $this->createLink($key, $parent, $navigationTitle);

        $link->setLinktype('internal');
        $link->setInternal($element->getId());
        $link->setInternalType(Service::getElementType($element));
        $link->setElement($element);

        return $link;
    }

    private function createDirectLink(
        string $key,
        Document $parent,
        string $navigationTitle,
    ): Link {
        $link = $this->createLink($key, $parent, $navigationTitle);

        $link->setLinktype('direct');
        $link->setDirect('https://example.com/' . $key);

        return $link;
    }

    private function createLink(
        string $key,
        Document $parent,
        string $navigationTitle,
    ): Link {
        $link = new Link();
        $link->setKey($key);
        $link->setParent($parent);

        $link->setProperty('navigation_title', 'text', $navigationTitle);
        $link->setProperty('navigation_name', 'text', $navigationTitle);

        return $link;
    }
}
