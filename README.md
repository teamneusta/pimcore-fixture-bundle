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

### Upgrading from earlier Version

Fixtures are now considered actual services and are loaded through Dependency Injection (DI).
To align with this approach,
you'll need to update your Fixture classes by moving service dependencies from the `create` method to the constructor.
If your Fixture relies on other Fixtures, implement the `HasDependencies` interface.

Here are the key changes:

1. **Fixture Interface Update**  
   The old fixture interface `Neusta\Pimcore\FixtureBundle\Fixture` has been replaced with `Neusta\Pimcore\FixtureBundle\Fixture\Fixture`. You can also extend from `Neusta\Pimcore\FixtureBundle\Fixture\AbstractFixture` to implement your Fixtures.

2. **Change in `create` Method**  
   The signature of the `create` method has been modified. It no longer takes any arguments, meaning all service dependencies must be specified via Dependency Injection. This is typically done through the constructor.

3. **Fixtures as Services**  
   Fixtures must be made available in the Dependency Injection container to be discovered. To do this, tag them with `neusta_pimcore_fixture.fixture`, or use autoconfiguration for automatic tagging.

4. **Specifying Inter-Fixture Dependencies**  
   If your Fixture depends on others, use the `HasDependencies` interface to specify these dependencies. Additional guidance is available in the section "[Referencing Fixtures and Depending on Other Fixtures](#referencing-fixtures-and-depending-on-other-fixtures)".

Make sure to update your Fixture classes according to these changes to ensure proper functionality and compatibility with this Bundle.

## Usage

### Writing Fixtures

Data fixtures are PHP service classes where you create objects and persist them to the database.

Imagine that you want to add some `Product` objects to your database.
To do this, create a fixture class and start adding products:

```php
use Neusta\Pimcore\FixtureBundle\Fixture\AbstractFixture;
use Pimcore\Model\DataObject\Product;

final class ProductFixture extends AbstractFixture
{
    public function create(): void
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

This process requires the `Group` fixture to exist before the `Product` fixture. You can achieve this ordering by implementing the `HasDependencies` interface.

```php
use Neusta\Pimcore\FixtureBundle\Fixture\AbstractFixture;
use Pimcore\Model\DataObject\ProductGroup;

final class ProductGroupFixture extends AbstractFixture
{
    public function create(): void
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
use Neusta\Pimcore\FixtureBundle\Fixture\AbstractFixture;
use Neusta\Pimcore\FixtureBundle\Fixture\HasDependencies;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductGroup;

final class ProductFixture extends AbstractFixture implements HasDependencies
{
    public function create(): void
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

To load fixtures in Tests, we offer the `SelectiveFixtureLoader`. To streamline your test setup, we recommend creating a base class with a method to load fixtures via the `SelectiveFixtureLoader`. Here's an example demonstrating how to implement this. 

```php
use Neusta\Pimcore\FixtureBundle\Fixture;
use Pimcore\Test\KernelTestCase;

abstract class BaseKernelTestCase extends KernelTestCase
{
    /**
     * @param list<class-string<Fixture>> $fixtures
     */
    protected function importFixtures(array $fixtures): void
    {
        /** @var SelectiveFixtureLoader $fixtureLoader */
        $fixtureLoader = static::getContainer()->get(SelectiveFixtureLoader::class);
        $fixtureLoader->setFixturesToLoad($fixtures)->loadFixtures();
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

To load fixtures in your local environment or as part of a deployment two commands are provided:
- `neusta:pimcore-fixture:load` (Loads a defined fixture class.)
- `neusta:pimcore-fixtures:load` (Loads all defined fixture classes.)

Beware that loading a large amount of objects may lead to a high consumption of memory.
Should you encounter memory issues when running the commands in `dev` environments you may want to try
setting the environment to `prod`. This appears to be beneficial in terms of pimcore's memory consumption. 

### Accessing Services from the Fixtures

As the Fixtures are just normal PHP Services you can use all DI features like constructor, setter or property injection as usual.

### Extension and customization through Events

The Bundle provides the following events to facilitate extensions and customization:

1. **`BeforeLoadFixtures`**  
   This event is triggered before any fixture is executed. It contains all the fixtures that are scheduled for execution, accessible via `$event->getFixtures()`. You can alter the list of fixtures to be loaded by using `$event->setFixtures(...)`.

2. **`AfterLoadFixtures`**  
   This event occurs after all relevant fixtures have been executed. It carries the fixtures that have been successfully loaded, which can be accessed through `$event->loadedFixtures`.

3. **`BeforeExecuteFixture`**  
   This event is triggered just before a fixture is executed. Using this event, you can prevent the execution of a specific fixture by setting `$event->setPreventExecution(true)`.

3. **`AfterExecuteFixture`**  
   This event occurs after a fixture has been executed.

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
