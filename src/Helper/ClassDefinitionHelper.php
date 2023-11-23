<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\ClassDefinition\Data\Select;

class ClassDefinitionHelper
{
    public static function createInput(string $name, string $title): Input
    {
        $input = new Input();
        $input->setName($name);
        $input->setTitle($title);

        return $input;
    }

    /**
     * @param array<array<string, string>> $options
     */
    public static function createSelect(string $name, string $title, array $options): Select
    {
        $select = new Select();
        $select->setName($name);
        $select->setTitle($title);
        $select->setOptions($options);

        return $select;
    }
}
