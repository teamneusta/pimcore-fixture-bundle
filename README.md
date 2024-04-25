# Pimcore Fixture Bundle

Provides a way to manage and execute the loading of data fixtures in Pimcore.

It can be useful for testing purposes, or for seeding a database with initial data.

## Installation

1. **Require the bundle**

   ```shell
   composer require teamneusta/pimcore-fixture-bundle
   ```

2. **Enable the bundle**

   Add the Bundle to your `config/bundles.php`:

   ```php
   Neusta\Pimcore\FixtureBundle\NeustaPimcoreFixtureBundle::class => ['test' => true],
   ```

## Usage

### Writing Fixtures

Data fixtures are PHP Service classes where you create objects and persist them to the database.

Imagine that you want to add some `Product` objects to your database.
To do this, create a fixture class and start adding products:

```php
use Neusta\Pimcore\FixtureBundle\Fixtures\AbstractFixture;
use Pimcore\Model\DataObject\Product;

final class ProductFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
            $product->setParentId(0);
            $product->setPublished(true);
            $product->setKey("Product {$i}");
            // ...

            $product->save();
        }
    }
}
```

### Referencing Fixtures and Depending on Other Fixtures

Suppose you want to link a `Product` fixture to a `Group` fixture. To do this, you need to create a `Group` fixture first and keep a reference to it. Later, you can use this reference when creating the `Product` fixture.

This process requires the `Group` fixture to exist before the `Product` fixture. You can achieve this ordering by implementing the `DependentFixtureInterface` interface.

```php
use Neusta\Pimcore\FixtureBundle\Fixtures\AbstractFixture;
use Pimcore\Model\DataObject\ProductGroup;

final class ProductGroupFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $productGroup = new ProductGroup();
        $productGroup->setParentId(0);
        $productGroup->setPublished(true);
        $productGroup->setKey('My Product Group');
        $productGroup->save();
        
        $this->addReference('my-product-group', $productGroup);
    }
}
```

```php
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Neusta\Pimcore\FixtureBundle\Fixtures\AbstractFixture;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductGroup;

final class ProductFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $productGroup = $this->getReference('my-product-group', ProductGroup::class);
    
        $product = new Product();
        $product->setParentId(0);
        $product->setPublished(true);
        $product->setKey('My grouped Product');
        $product->setProductGroup($productGroup);
        $product->save();
    }

    public function getDependencies(): array
    {
        return [
            ProductGroupFixture::class,
        ];
    }
}
```

### Loading Fixtures

The Doctrine Fixtures Bundle doesn't natively support loading fixtures in tests or from other code. To address this, we offer an `InTestFixtureLoader`. To streamline your test setup, we recommend creating a base class with a method to load fixtures via the `InTestFixtureLoader`. Here's an example demonstrating how to implement this. 

```php
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Test\KernelTestCase;

abstract class BaseKernelTestCase extends KernelTestCase
{
    /** @param list<string> $fixtureGroups */
    protected function importFixtures(array $fixtureGroups): void
    {
        (new InTestFixtureLoader(static::$kernel))->load($fixtureGroups);
    }

    protected function tearDown(): void
    {
        \Pimcore\Cache::clearAll();
        \Pimcore::collectGarbage();

        parent::tearDown();
    }
}
```

Use the base class as follows:

```php
use Pimcore\Model\DataObject;

final class MyCustomTest extends BaseKernelTestCase
{
    /** @test */
    public function import_fixtures(): void
    {
        $this->importFixtures([
            ProductFixture::class,
        ]);

        $productFixture = DataObject::getByPath('/product-1');

        self::assertNotNull($productFixture);
    }
}
```

### Accessing Services from the Fixtures

As the Fixtures are just normal PHP Services you can use all DI features like constructor, setter or property injection as usual.

## Contribution

Feel free to open issues for any bug, feature request, or other ideas.

Please remember to create an issue before creating large pull requests.

### Local Development

To develop on a local machine, the vendor dependencies are required.

```shell
bin/composer install
```

We use composer scripts for our main quality tools. They can be executed via the `bin/composer` file as well.

```shell
bin/composer cs:fix
bin/composer phpstan
bin/composer tests
```
