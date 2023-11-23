<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

class ClassificationstoreHelper
{
    public static function createStoreConfig(string $name): StoreConfig
    {
        if (!$classificationstoreVariants = StoreConfig::getByName($name)) {
            $classificationstoreVariants = new StoreConfig();
            $classificationstoreVariants->setName($name);
            $classificationstoreVariants->save();
        }

        return $classificationstoreVariants;
    }

    public static function createKeyConfig(string $name, string $description, Data $field, int $storeId): KeyConfig
    {
        if (!$keyConfig = KeyConfig::getByName($name)) {
            $keyConfig = new KeyConfig();
            $keyConfig->setName($name);
            $keyConfig->setDescription($description);
            $keyConfig->setEnabled(true);
            $keyConfig->setType($field->getFieldtype());
            $keyConfig->setDefinition(\json_encode($field));
            $keyConfig->setStoreId($storeId);
            $keyConfig->save();
        }

        return $keyConfig;
    }

    public static function createGroupConfig(string $name, string $description, int $storeId): ?GroupConfig
    {
        if (!$groupConfig = GroupConfig::getByName($name)) {
            $groupConfig = new GroupConfig();
            $groupConfig->setName($name);
            $groupConfig->setDescription($description);
            $groupConfig->setStoreId($storeId);
            $groupConfig->save();
        }

        return $groupConfig;
    }

    public static function createKeyGroupRelation(int $keyConfigId, int $groupConfigId): KeyGroupRelation
    {
        if (!$keyGroupRelation = KeyGroupRelation::getByGroupAndKeyId(
            $groupConfigId,
            $keyConfigId,
        )) {
            $keyGroupRelation = new KeyGroupRelation();
            $keyGroupRelation->setKeyId($keyConfigId);
            $keyGroupRelation->setGroupId($groupConfigId);
            $keyGroupRelation->save();
        }

        return $keyGroupRelation;
    }

    public static function createNewEntriesForClassificationValues(
        string $keyConfigName,
        Data $field,
        StoreConfig $storeConfig,
    ): array {
        $keyConfig = ClassificationstoreHelper::createKeyConfig(
            $keyConfigName,
            'Created by fixture',
            $field,
            $storeConfig->getId(),
        );

        // GroupConfig
        $groupConfig = ClassificationstoreHelper::createGroupConfig(
            $keyConfigName,
            'Created by fixture',
            $storeConfig->getId(),
        );

        // KeyGroupRelation
        ClassificationstoreHelper::createKeyGroupRelation(
            $keyConfig->getId(),
            $groupConfig->getId(),
        );

        return [$keyConfig, $groupConfig];
    }
}
