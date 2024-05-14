<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Doctrine\DBAL\Logging\SQLLogger;
use Neusta\Pimcore\FixtureBundle\Event\AfterExecuteFixture;
use Neusta\Pimcore\FixtureBundle\Event\AfterLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Event\BeforeLoadFixtures;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class PimcoreLoadOptimization implements EventSubscriberInterface
{
    private ?SQLLogger $originalSqlLogger = null;
    private bool $versionEnabled;
    private bool $cacheEnabled;

    public function __construct(
        private readonly ProfilerDisabler $profilerDisabler,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLoadFixtures::class => 'beforeCommand',
            AfterLoadFixtures::class => 'afterCommand',
            AfterExecuteFixture::class => 'afterExecute',
        ];
    }

    public function beforeCommand(): void
    {
        $this->versionEnabled = Version::isEnabled();
        $this->cacheEnabled = Cache::isEnabled();
        Version::disable();
        Cache::disable();

        $this->originalSqlLogger = Db::getConnection()->getConfiguration()->getSQLLogger();
        Db::getConnection()->getConfiguration()->setSQLLogger(null);

        $this->profilerDisabler->disable();
    }

    public function afterCommand(): void
    {
        Db::getConnection()->getConfiguration()->setSQLLogger($this->originalSqlLogger);

        $this->versionEnabled && Version::enable();
        $this->cacheEnabled && Cache::enable();
    }

    public function afterExecute(): void
    {
        \Pimcore::collectGarbage();
    }
}
