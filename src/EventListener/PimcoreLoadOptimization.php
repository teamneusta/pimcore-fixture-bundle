<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Neusta\Pimcore\FixtureBundle\Event\AfterLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Event\BeforeLoadFixtures;
use Neusta\Pimcore\FixtureBundle\Logger\LoggingState;
use Pimcore\Cache;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class PimcoreLoadOptimization implements EventSubscriberInterface
{
    private bool $versionEnabled;
    private bool $cacheEnabled;

    public function __construct(
        private readonly LoggingState $loggingState,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLoadFixtures::class => 'beforeCommand',
            AfterLoadFixtures::class => 'afterCommand',
        ];
    }

    public function beforeCommand(): void
    {
        $this->versionEnabled = Version::isEnabled();
        $this->cacheEnabled = Cache::isEnabled();

        Version::disable();
        Cache::disable();
        $this->loggingState->disable();
    }

    public function afterCommand(): void
    {
        $this->versionEnabled && Version::enable();
        $this->cacheEnabled && Cache::enable();
        $this->loggingState->enable();
    }
}
