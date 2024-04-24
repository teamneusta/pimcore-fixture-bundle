<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\EventListener;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\DBAL\Logging\SQLLogger;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Model\Version;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreLoadOptimization implements EventSubscriberInterface
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
            ConsoleEvents::COMMAND => 'beforeCommand',
            ConsoleEvents::TERMINATE => 'afterCommand',
        ];
    }

    public function beforeCommand(ConsoleCommandEvent $event): void
    {
        if (!$this->isRelevantCommand($event)) {
            return;
        }

        $output = $event->getOutput();
        $output->writeln('Disabling <info>Pimcore Versioning</info>, <info>Pimcore Cache</info> and <info>Pimcore SQL Logger</info>');

        $this->versionEnabled = Version::isEnabled();
        $this->cacheEnabled = Cache::isEnabled();
        Version::disable();
        Cache::disable();

        $this->originalSqlLogger = Db::getConnection()->getConfiguration()->getSQLLogger();
        Db::getConnection()->getConfiguration()->setSQLLogger(null);

        $this->profilerDisabler->disable();
    }

    public function afterCommand(ConsoleTerminateEvent $event): void
    {
        if (!$this->isRelevantCommand($event)) {
            return;
        }

        $this->profilerDisabler->enable();

        Db::getConnection()->getConfiguration()->setSQLLogger($this->originalSqlLogger);

        $this->versionEnabled && Version::enable();
        $this->cacheEnabled && Cache::enable();
    }

    private function isRelevantCommand(ConsoleEvent $event): bool
    {
        $command = $event->getCommand();

        return $command instanceof LoadDataFixturesDoctrineCommand;
    }
}
