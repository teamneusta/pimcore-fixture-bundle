# Pimcore Fixture Bundle

Provides a way to manage and execute the loading of data fixtures in Pimcore.

It can be useful for testing purposes or for seeding a database with initial data.

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

3. **Register your Fixtures Folder for Service autoconfiguration**

   Depending on where you want to create your Fixtures, or if they should only be accessible during test execution.

   ```yaml
   when@test:
     services:
       App\Tests\Fixture\:
         autoconfigure: true
         resource: '../tests/Fixture/'
   ```

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
            $product->setParentId(1);
            $product->setPublished(true);
            $product->setKey("Product {$i}");
            // ...

            $product->save();
        }
    }
}
```

### Referencing Fixtures and Depending on Other Fixtures

Suppose you want to link a `Product` fixture to a `Group` fixture. 
To do this, you need to create a `Group` fixture first and keep a reference to it. 
Later you can use this reference when creating the `Product` fixture.

This process requires the `Group` fixture to exist before the `Product` fixture. 
You can achieve this ordering by implementing the `HasDependencies` interface.

```php
use Neusta\Pimcore\FixtureBundle\Fixture\AbstractFixture;
use Pimcore\Model\DataObject\ProductGroup;

final class ProductGroupFixture extends AbstractFixture
{
    public function create(): void
    {
        $productGroup = new ProductGroup();
        $productGroup->setParentId(1);
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
        $product->setParentId(1);
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

#### In Tests

To load fixtures in Tests, we offer the `SelectiveFixtureLoader`. 
To streamline your test setup, we recommend creating a base class with a method to load fixtures via the `SelectiveFixtureLoader`. 
Here’s an example demonstrating how to implement this. 

```php
use Neusta\Pimcore\FixtureBundle\Fixture\Fixture;
use Neusta\Pimcore\FixtureBundle\FixtureLoader\SelectiveFixtureLoader;
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

#### As Initial Data in Your Project

To load fixtures in your local environment or as part of a deployment, two commands are provided:
- `neusta:pimcore-fixture:load` (Loads a defined fixture class.)
- `neusta:pimcore-fixtures:load` (Loads all defined fixture classes.)

Beware that loading a large number of objects may lead to high memory consumption.
Should you encounter memory issues when running the commands in `dev` environments you may want to try setting the environment to `prod`.
Disabling the debug mode also seems beneficial in terms of memory consumption. 

For example, provide these options when using the symfony console: 
```shell
bin/console --env=prod --no-debug neusta:pimcore-fixtures:load
```

### Accessing Services From the Fixtures

As the Fixtures are just normal PHP services, you can use all DI features like constructor, setter, or property injection as usual.

### Extension and Customization Through Events

The Bundle provides the following events to facilitate extensions and customization:

1. **`BeforeLoadFixtures`**  
   This event is dispatched before any fixture is executed. 
   It contains all the fixtures that are scheduled for execution, accessible via `$event->fixtures`. 
   You can alter the list of fixtures to be loaded by modifying it `$event->fixtures = ...`.

2. **`BeforeExecuteFixture`**  
   This event is dispatched for each fixture just before it is executed.
   Using this event, you can prevent the execution of a specific fixture by setting `$event->preventExecution = true`.

3. **`AfterExecuteFixture`**  
   This event is dispatched for each fixture after it has been executed.

4. **`AfterLoadFixtures`**  
   This event is dispatched after all relevant fixtures have been executed. 
   It carries the fixtures that have been successfully loaded, which can be accessed through `$event->loadedFixtures`.

## Contribution

Feel free to open issues for any bug, feature request, or other ideas.

Please remember to create an issue before creating large pull requests.

### Local Development

To develop on your local machine, instance identification for Pimcore 12 is needed.

Copy the `compose.override.yaml.dist` file to `compose.override.yaml`:

```shell
cp -n compose.override.yaml.dist compose.override.yaml
```

And replace all `replace_with_secret` values with your data.

Then install the dependencies:

```shell
bin/composer install
```

We use composer scripts for our main quality tools. They can be executed via the `bin/composer` file as well.

```shell
bin/composer cs:fix
bin/composer phpstan
```

For the tests there is a different script that includes a database setup.

```shell
bin/run-tests
