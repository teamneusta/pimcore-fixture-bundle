<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Documents;

use Pimcore\Model\Document;

trait CreateDocumentTrait
{
    private function createPage(
        string $key,
        Document|Document\Folder $parent,
        string $title,
        string $controller,
        string $template,
        bool $published = false,
        bool $withSave = false,
    ): Document\Page {
        $page = new Document\Page();
        $page->setKey($key);
        $page->setParent($parent);
        $page->setTitle($title);
        $page->setDescription('');
        $page->setController($controller);
        $page->setTemplate($template);
        $page->setPublished($published);
        if ($withSave) {
            $page->save();
        }

        return $page;
    }

    private function createEmail(
        string $key,
        Document $parent,
        string $controller,
        string $template,
        bool $published = false,
        bool $withSave = false,
    ): Document\Email {
        $email = new Document\Email();
        $email->setKey($key);
        $email->setParent($parent);
        $email->setController($controller);
        $email->setTemplate($template);
        $email->setPublished($published);
        if ($withSave) {
            $email->save();
        }

        return $email;
    }
}
