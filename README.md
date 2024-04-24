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

### Referencing Fixtures

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

To use fixtures in tests, a few preparations must be made.

Currently, the `FixtureFactory` still has to be instantiated manually.
The easiest way to do this is with a project-specific kernel base class.

```php
use Neusta\Pimcore\FixtureBundle\Factory\FixtureFactory;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiatorForAll;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiatorForParametrizedConstructors;
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Test\KernelTestCase;

abstract class BaseKernelTestCase extends KernelTestCase
{
    protected FixtureFactory $fixtureFactory;

    /** @param list<class-string<Fixture>> $fixtures */
    protected function importFixtures(array $fixtures): void
    {
        $this->fixtureFactory ??= (new FixtureFactory([
            new FixtureInstantiatorForParametrizedConstructors(static::getContainer()),
            new FixtureInstantiatorForAll(),
        ]));

        $this->fixtureFactory->createFixtures($fixtures);
    }

    protected function tearDown(): void
    {
        unset($this->fixtureFactory);
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

Sometimes you may need to access your application's services inside a fixture class.
You can use normal dependency injection for this:

> [!IMPORTANT]
> You need to create your `FixtureFactory` with the `FixtureInstantiatorForParametrizedConstructors` for this to work!

```php
final class SomeFixture implements Fixture
{
    public function __construct(
        private Something $something,
    ) {
    }

    public function create(): void
    {
        // ... use $this->something
    }
}
```

### Depending on Other Fixtures

In a fixture, you can depend on other fixtures.
Therefore, you have to reference them in your `create()` method as parameters.

> [!IMPORTANT]
> All parameters of the `create()` method in your fixtures may *only* reference other fixtures.
> Everything else is not allowed!

Referencing other fixtures ensures they are created before this one.

This also allows accessing some state of the other fixtures.

```php
final class SomeFixture implements Fixture
{
    public function create(OtherFixture $otherFixture): void
    {
        // do something with $otherFixture->someInformation
    }
}

final class OtherFixture implements Fixture
{
    public string $someInformation;

    public function create(): void
    {
        $this->someInformation = 'some information created in this fixture';
    }
}
```

The state can also be accessed from the tests:

```php
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Model\DataObject\Product;

final class ProductFixture implements Fixture
{
    public int $productId;

    public function create(): void
    {
        $product = new Product();
        $product->setParentId(0);
        $product->setPublished(true);
        $product->setKey("Product Fixture");
        // ...

        $product->save();

        $this->productId = $product->getId();
    }
}
```

```php
use Pimcore\Model\DataObject;

final class MyCustomTest extends BaseKernelTestCase
{
    /** @test */
    public function some_product_test(): void
    {
        $this->importFixtures([
            ProductFixture::class,
        ]);

        $productFixture = $this->fixtureFactory->getFixture(ProductFixture::class);
        $product = DataObject::getById($productFixture->productId);

        self::assertNotNull($product);
    }
}
```

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
