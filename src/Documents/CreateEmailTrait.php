<?php declare(strict_types=1);

namespace NspPimcore\FixtureBase\Documents;

use Pimcore\Model\Document;

trait CreateEmailTrait
{
    private function createEmailDocument(
        string $key,
        Document|Document\Folder|null $emailRootFolderEn,
        string $controllerMethod,
        string $subject,
        string $from,
        string $to,
    ): Document\Email {
        $email = new Document\Email();
        $email->setKey($key);
        $email->setParent($emailRootFolderEn);
        $email->setSubject($subject);
        $email->setFrom($from);
        $email->setTo($to);
        $email->setController($controllerMethod);

        return $email;
    }
}
