<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Command;

use Neusta\Pimcore\FixtureBundle\Event\BeforeExecuteFixture;
use Neusta\Pimcore\FixtureBundle\FixtureLoader\FixtureLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'neusta:pimcore-fixtures:load',
    description: 'Loads all defined fixture classes.',
)]
final class LoadFixturesCommand extends Command
{
    public function __construct(
        private readonly FixtureLoader $fixtureLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(
            <<<'EOF'
            The <info>%command.name%</info> command loads all available fixtures.

            <info>Important:</info> This command <comment>must not</comment> be executed twice. On a second execution,
            this command would fail, because the keys of the Pimcore data objects are already present.
            To re-run the command, please reset the database first.
            EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Loading fixtures');
        $output->writeln('');

        $this->eventDispatcher->addListener(BeforeExecuteFixture::class, function (BeforeExecuteFixture $event) use ($output) {
            $output->writeln(\sprintf(
                ' - Loading <info>%s</info>',
                $event->fixture::class
            ));
        });

        $this->fixtureLoader->loadFixtures();

        $output->writeln('Loading fixtures completed.');

        return Command::SUCCESS;
    }
}
